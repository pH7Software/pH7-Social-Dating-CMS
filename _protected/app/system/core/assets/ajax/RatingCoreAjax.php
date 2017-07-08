<?php
/**
 * @title          Rating Ajax Class
 * @desc           Simple Rating Page Class with Ajax.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.2
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Session\Session;

class RatingCoreAjax
{
    /** @var HttpRequest */
    private $_oHttpRequest;

    /** @var RatingCoreModel */
    private $_oRatingModel;

    /** @var string */
    private $_sTxt;

    /** @var string */
    private $_sTable;

    /** @var int */
    private $_iStatus;

    /** @var int */
    private $_iId;

    /** @var float */
    private $_fScore;

    /** @var int */
    private static $_iVotes;

    public function __construct()
    {
        $this->_oHttpRequest = new HttpRequest;

        if ($this->_oHttpRequest->postExists('action') &&
            $this->_oHttpRequest->postExists('table') &&
            $this->_oHttpRequest->postExists('score') &&
            $this->_oHttpRequest->postExists('id')
        ) {
            if ($this->_oHttpRequest->post('action') == 'rating') {
                // Only for the Members
                if (!UserCore::auth()) {
                    $this->_iStatus = 0;
                    $this->_sTxt = t('Please <b>register</b> or <b>login</b> to vote.');
                } else {
                    $this->initialize();
                }
            }
        } else {
            Http::setHeadersByCode(400);
            exit('Bad Request Error!');
        }
    }

    /**
     * Displays the votes.
     *
     * @return string
     */
    public function show()
    {
        return jsonMsg($this->_iStatus, $this->_sTxt);
    }

    /**
     * Initialize the methods of the class.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->_oRatingModel = new RatingCoreModel;
        $this->_sTable = $this->_oHttpRequest->post('table');
        $this->_iId = (int) $this->_oHttpRequest->post('id');

        if ($this->_sTable == 'Members') {
            $iProfileId = (int) (new Session)->get('member_id');

            if ($iProfileId === $this->_iId) {
                $this->_iStatus = 0;
                $this->_sTxt = t('You can not vote your own profile!');
                return;
            }
        }

        /**
         * @internal Today's IP address is also easier to change than delete a cookie, so we have chosen the Cookie instead save the IP address in the database.
         */
        $oCookie = new Cookie;
        $sCookieName = 'pHSVoting' . $this->_iId . $this->_sTable;
        if ($oCookie->exists($sCookieName)) {
            $this->_iStatus = 0;
            $this->_sTxt = t('You have already voted!');
            return;
        } else {
            $oCookie->set($sCookieName, 1, 3600 * 24 * 7); // A week
        }
        unset($oCookie);

        $this->select();
        $this->update();
        $this->_iStatus = 1;
        $sVoteTxt = (static::$_iVotes > 1) ? t('Votes') : t('Vote');
        $this->_sTxt = t('Score: %0% - %2%: %1%', number_format($this->_fScore / static::$_iVotes, 1), static::$_iVotes, $sVoteTxt);
    }

    /**
     * Adds voting in the database and increment the static attribute to vote.
     *
     * @return void
     */
    protected function select()
    {
        $iVotes = $this->_oRatingModel->getVote($this->_iId, $this->_sTable);
        $fRate = $this->_oRatingModel->getScore($this->_iId, $this->_sTable);

        static::$_iVotes = $iVotes += 1;
        $fScore = (float)$this->_oHttpRequest->post('score');

        $this->_fScore = $fRate += $fScore;
    }

    /**
     * Updates the vote in the database.
     *
     * @return void
     */
    protected function update()
    {
        $this->_oRatingModel->updateVotes($this->_iId, $this->_sTable);
        $this->_oRatingModel->updateScore($this->_fScore, $this->_iId, $this->_sTable);
    }

}

echo (new RatingCoreAjax)->show();
