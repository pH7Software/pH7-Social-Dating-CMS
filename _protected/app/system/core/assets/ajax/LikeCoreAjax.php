<?php
/**
 * @title          Like Ajax Class
 * @desc           Simple Like Page Ajax Class.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.1
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Request\Http as HttpRequest;

class LikeCoreAjax
{
    /** @var int */
    private static $iVotesLike = 0;

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var LikeCoreModel */
    private $oLikeModel;

    /** @var string */
    private $sKey;

    /** @var float */
    private $sLastIp;

    /** @var string */
    private $sLastIpVoted;

    /** @var int */
    private $iVote;

    public function __construct()
    {
        $this->oHttpRequest = new HttpRequest;

        ($this->oHttpRequest->postExists('key') ? $this->initialize() : exit('-1'));
    }

    /**
     * Showing votes.
     *
     * @return string
     */
    public function show()
    {
        $sTxt = (static::$iVotesLike > 1) ?
            nt('You and one other have voted for this!', 'You and %n% other people have voted for this!', static::$iVotesLike - 1) :
            t('Congrats! You are the first to like it');

        return '{"votes":' . static::$iVotesLike . ',"txt":"' . $sTxt . '"}';
    }

    /**
     * Initialize the methods of the class.
     *
     * @return void
     */
    private function initialize()
    {
        $this->oLikeModel = new LikeCoreModel;
        $this->sKey = $this->oHttpRequest->post('key');
        $this->iVote = $this->oHttpRequest->postExists('vote');
        $this->sLastIp = Ip::get();
        $this->select();
    }

    /**
     * Gets the likes and insert it into the DB if it's the first like, otherwise update the like.
     *
     * @return void
     */
    private function select()
    {
        $oResult = $this->oLikeModel->select($this->sKey);

        if (!empty($oResult)) {
            foreach ($oResult as $mRow) {
                static::$iVotesLike = (int)$mRow->votes;
                $this->sLastIpVoted = $mRow->lastIp;
            }

            if ($this->isUserVoting()) {
                $this->update();
            }
        } elseif ($this->isUserVoting()) {
            $this->insert();
        }
    }

    /**
     * Check the permissions so only members can like, but you can disable this check so even visitors will be able to like pages.
     *
     * @return bool Returns true if the user is connected, false otherwise.
     */
    private function checkPerm()
    {
        return UserCore::auth();
    }

    /**
     * Adds voting into the database and increment the static vote attribute.
     *
     * @return void
     */
    private function insert()
    {
        static::$iVotesLike++;
        $this->oLikeModel->insert($this->sKey, $this->sLastIp);
    }

    /**
     * Updates the like into the database.
     *
     * @return void
     */
    private function update()
    {
        if ($this->sLastIpVoted != $this->sLastIp) {
            static::$iVotesLike++;
            $this->oLikeModel->update($this->sKey, $this->sLastIp);
        }
    }

    /**
     * @return bool
     */
    private function isUserVoting()
    {
        return $this->iVote && $this->checkPerm();
    }
}

echo (new LikeCoreAjax)->show();
