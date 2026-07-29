<?php

/**
 * @title          Rating Ajax Class
 *
 * @desc           Simple Rating Page Class with Ajax.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @version        1.2
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Session\Session;
use PH7\JustHttp\StatusCode;

class RatingCoreAjax
{
    private const MIN_SCORE = 0.1;
    private const MAX_SCORE = 5.0;

    /**
     * Cache lifetime set to 1 week.
     */
    private const COOKIE_LIFETIME = 3600 * 24 * 7;

    private HttpRequest $oHttpRequest;

    private RatingCoreModel $oRatingModel;

    private string $sTxt;

    private string $sTable;

    private static int $iVotes;

    private int $iStatus;

    private int $iId;

    private float $fScore;

    public function __construct()
    {
        $this->oHttpRequest = new HttpRequest();

        if ($this->isValidRequestToRate()) {
            if ($this->oHttpRequest->post('action') === 'rating') {
                // Only for the Members
                if (!UserCore::auth()) {
                    $this->iStatus = 0;
                    $this->sTxt = t('Please <b>register</b> or <b>login</b> to vote.');
                } elseif (!(new Token())->checkUrl()) {
                    $this->iStatus = 0;
                    $this->sTxt = Form::errorTokenMsg();
                } else {
                    $this->initialize();
                }
            }
        } else {
            Http::setHeadersByCode(StatusCode::BAD_REQUEST);
            exit('Bad Request Error!');
        }
    }

    /**
     * Displays the votes.
     */
    public function show(): string
    {
        return jsonMsg($this->iStatus, $this->sTxt);
    }

    /**
     * Initialize the methods of the class.
     */
    private function initialize(): void
    {
        $this->oRatingModel = new RatingCoreModel();
        $this->sTable = $this->oHttpRequest->post('table');
        $this->iId = (int)$this->oHttpRequest->post('id');
        $fScore = (float)$this->oHttpRequest->post('score');

        if ($fScore < self::MIN_SCORE || $fScore > self::MAX_SCORE) {
            $this->iStatus = 0;
            $this->sTxt = t('The rating score is invalid.');

            return;
        }

        if ($this->hasCorrectDbTable()) {
            $iProfileId = (int)(new Session())->get('member_id');

            if ($iProfileId === $this->iId) {
                $this->iStatus = 0;
                $this->sTxt = t('You can not vote your own profile!');

                return;
            }
        }

        if (!$this->isEligibleToVote()) {
            return;
        }

        $this->select($fScore);
        $this->update();
        $this->iStatus = 1;
        $sVoteTxt = self::$iVotes > 1 ? t('Votes') : t('Vote');
        $this->sTxt = t(
            'Score: %0% - %2%: %1%',
            number_format($this->fScore / self::$iVotes, 1),
            self::$iVotes,
            $sVoteTxt
        );
    }

    /**
     * Adds voting in the database and increment the static attribute to vote.
     */
    private function select(float $fSubmittedScore): void
    {
        $iVotes = $this->oRatingModel->getVote($this->iId, $this->sTable);
        $fRate = $this->oRatingModel->getScore($this->iId, $this->sTable);

        self::$iVotes = ++$iVotes;
        $this->fScore = $fRate + $fSubmittedScore;
    }

    /**
     * Updates the vote in the database.
     */
    private function update(): void
    {
        $this->oRatingModel->updateVotes($this->iId, $this->sTable);
        $this->oRatingModel->updateScore($this->fScore, $this->iId, $this->sTable);
    }

    private function isEligibleToVote(): bool
    {
        /**
         * @internal in today's world, IP address is also easier to change than deleting a cookie,
         * so we have chosen the cookie approach instead of saving the IP address in the database
         */
        $oCookie = new Cookie();
        $sCookieName = 'pHSVoting' . $this->iId . $this->sTable;
        if ($oCookie->exists($sCookieName)) {
            $this->iStatus = 0;
            $this->sTxt = t('You have already voted!');
            $bIsEligible = false;
        } else {
            $oCookie->set($sCookieName, '1', self::COOKIE_LIFETIME);
            $bIsEligible = true;
        }
        unset($oCookie);

        return $bIsEligible;
    }

    private function isValidRequestToRate(): bool
    {
        return $this->oHttpRequest->postExists('action')
            && $this->oHttpRequest->postExists('table')
            && $this->oHttpRequest->postExists('score')
            && $this->oHttpRequest->postExists('id');
    }

    private function hasCorrectDbTable(): bool
    {
        return $this->sTable === DbTableName::MEMBER;
    }
}

echo (new RatingCoreAjax())->show();
