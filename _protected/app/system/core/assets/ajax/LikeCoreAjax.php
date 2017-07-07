<?php
/**
 * @title          Like Ajax Class
 * @desc           Simple Like Page Ajax Class.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.1
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Request\Http;

class LikeCoreAjax
{

    private static $_iVotesLike = 0;
    private $_oHttpRequest, $_oLikeModel, $_sKey, $_iVote, $_fLastIp, $_fLastIpVoted;

    public function __construct()
    {
        $this->_oHttpRequest = new Http;

        ($this->_oHttpRequest->postExists('key') ? $this->initialize() : exit('-1'));
    }

    /**
     * Showing votes.
     *
     * @access public
     * @return string
     */
    public function show()
    {
        $sTxt = (static::$_iVotesLike > 1) ? nt('You and one other have voted for this!', 'You and %n% other people have voted for this!', static::$_iVotesLike-1) : t('Congrats! You are the first to like it');
        return '{"votes":' . static::$_iVotesLike . ',"txt":"' . $sTxt . '"}';
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
     * Gets the likes and insert it into the DB if it's the first like, otherwise update the like.
     *
     * @access protected
     * @return void
     */
    protected function select()
    {
        $oResult = $this->_oLikeModel->select($this->_sKey);
        if (!empty($oResult)) {
            foreach ($oResult as $mRow) {
                static::$_iVotesLike = (int)$mRow->votes;
                $this->_fLastIpVoted = $mRow->lastIp;
            }
            if ($this->_iVote)
                if ($this->checkPerm()) $this->update();
        } else {
            if ($this->_iVote)
                if ($this->checkPerm()) $this->insert();
        }
    }

    /**
     * Check the permissions so only members can like, but you can disable this check so even visitors will be able to like pages.
     *
     * @access protected
     * @return boolean Returns true if the user is connected, false otherwise.
     */
    protected function checkPerm()
    {
        return (UserCore::auth()) ? true : false;
    }

    /**
     * Adds voting into the database and increment the static vote attribute.
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
     * Updates the like into the database.
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

}

echo (new LikeCoreAjax)->show();
