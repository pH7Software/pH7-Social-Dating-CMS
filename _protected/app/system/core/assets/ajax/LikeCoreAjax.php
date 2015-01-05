<?php
/**
 * @title          Like Ajax Class
 * @desc           Simple Like Page Ajax Class.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.1
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http, PH7\Framework\Ip\Ip;

class LikeCoreAjax
{

    private $_oHttpRequest, $_oLikeModel, $_sTxt, $_sKey, $_iVote, $_fLastIp, $_fLastIpVoted;
    private static $_iVotesLike = 0;

    public function __construct()
    {
        $this->_oHttpRequest = new Http;

        ($this->_oHttpRequest->postExists('key') ? $this->initialize() : exit('-1'));
    }

    /**
     * Initialize the methods of the class.
     *
     * @access protected
     * @return void
     */
    protected function initialize()
    {
        $this->_oLikeModel = new LikeCoreModel;
        $this->_sKey = $this->_oHttpRequest->post('key');
        $this->_iVote = $this->_oHttpRequest->postExists('vote');
        $this->_fLastIp = Ip::get();
        $this->select();
    }

    /**
     * Gets the votes in the database and inserts the new vote if otherwise updates the vote.
     *
     * @access protected
     * @return void
     */
    protected function select()
    {
        $oResult = $this->_oLikeModel->select($this->_sKey);
        if(!empty($oResult))
        {
            foreach($oResult as $mRow)
            {
                 static::$_iVotesLike = (int)$mRow->votes;
                 $this->_fLastIpVoted = $mRow->lastIp;
            }
            if($this->_iVote)
                if($this->checkPerm()) $this->update();
        }
        else
        {
            if($this->_iVote)
                if($this->checkPerm()) $this->insert();
        }
    }

    /**
     * Check the permissions so only members can vote, but you can disable this check so that even visitors vote page.
     *
     * @access protected
     * @return boolean Returns true if the user is connected, false otherwise.
     */
    protected function checkPerm()
    {
        // Only for members
        if(!UserCore::auth())
        {
            $this->_sTxt = t('Please <b>register</b> or <b>login</b> to vote this.');
            return false;
        }
        return true;
    }

    /**
     * Adds voting in the database and increment the static attribute to vote.
     *
     * @access protected
     * @return void
     */
    protected function insert()
    {
        static::$_iVotesLike++;
        $this->_oLikeModel->insert($this->_sKey, $this->_fLastIp);
    }

    /**
     * Updates the vote in the database.
     *
     * @access protected
     * @return void
     */
    protected function update()
    {
        if($this->_fLastIpVoted != $this->_fLastIp)
        {
            static::$_iVotesLike++;
            $this->_oLikeModel->update($this->_sKey, $this->_fLastIp);
        }
    }

    /**
     * Showing votes.
     *
     * @access public
     * @return string
     */
    public function show()
    {
        $sTxt = (!empty($this->_sTxt) ? $this->_sTxt : ((static::$_iVotesLike > 1) ? t('peoples have voted for this!') : t('people have voted for this!')));
        return '{"votes":' . static::$_iVotesLike . ',"txt":"' . $sTxt . '"}';
    }

    public function __destruct()
    {
        unset(
           $this->_oHttpRequest,
           $this->_oLikeModel,
           $this->_sTxt,
           $this->_sKey,
           $this->_iVote,
           $this->_fLastIp,
           $this->_fLastIpVoted
        );
    }

}

echo (new LikeCoreAjax)->show();
