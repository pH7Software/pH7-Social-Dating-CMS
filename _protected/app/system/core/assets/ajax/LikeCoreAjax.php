<?php
/**
 * @desc           Simple Like Page Ajax Class.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Asset / Ajax
 * @version        1.2
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Mvc\Request\Http as HttpRequest;

class LikeCoreAjax
{
    private static int $iVotesLike = 0;

    private HttpRequest $oHttpRequest;

    private LikeCoreModel $oLikeModel;

    private string $sKey;

    private string $sLastIpVoted;

    private string $sLastIp;

    private int $iVote;

    public function __construct()
    {
        $this->oHttpRequest = new HttpRequest;

        ($this->oHttpRequest->postExists('key') ? $this->initialize() : exit('-1'));
    }

    /**
     * Showing votes.
     */
    public function show(): string
    {
        $sTxt = (static::$iVotesLike > 1) ?
            nt('You and one other have voted for this!', 'You and %n% other people have voted for this!', static::$iVotesLike - 1) :
            t("Congrats! You're the first one!");

        return '{"votes":' . static::$iVotesLike . ',"txt":"' . $sTxt . '"}';
    }

    /**
     * Initialize the methods of the class.
     */
    private function initialize(): void
    {
        $this->oLikeModel = new LikeCoreModel;
        $this->sKey = $this->oHttpRequest->post('key');
        $this->iVote = (int)$this->oHttpRequest->post('vote');
        $this->sLastIp = Ip::get();
        $this->select();
    }

    /**
     * Gets the likes and insert it into the DB if it's the first like, otherwise update the like.
     */
    private function select(): void
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
    private function checkPerm(): bool
    {
        return UserCore::auth();
    }

    /**
     * Adds voting into the database and increment the static vote attribute.
     */
    private function insert(): void
    {
        static::$iVotesLike++;
        $this->oLikeModel->insert($this->sKey, $this->sLastIp);
    }

    /**
     * Updates the like into the database.
     */
    private function update(): void
    {
        if ($this->sLastIpVoted != $this->sLastIp) {
            static::$iVotesLike++;
            $this->oLikeModel->update($this->sKey, $this->sLastIp);
        }
    }

    private function isUserVoting(): bool
    {
        return $this->iVote && $this->checkPerm();
    }
}

echo (new LikeCoreAjax)->show();
