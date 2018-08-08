<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Date\Various as VDate;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class ValidateSiteCore
{
    const SESS_IS_VISITED = 'suggestionbox_visited';
    const VALIDATE_FORM_PAGE_DELAY = '2 months';

    /**
     * Add more "true" or "false" to give
     * more/less probability of showing up the dialog.
     */
    const STATUS = [
        true,
        true,
        true,
        false
    ];

    const VALIDATE_FORM_POPUP_DELAYS = [
        '4 hours',
        '1 day',
        '2 days',
        '3 days',
        '5 days',
        '8 days',
        '13 days',
        '21 days'
    ];

    /** @var ValidateSiteCoreModel */
    private $oValidateSiteModel;

    /** @var Session */
    private $oSession;

    public function __construct(ValidateSiteCoreModel $oValidateSiteModel, Session $oSession)
    {
        $this->oValidateSiteModel = $oValidateSiteModel;
        $this->oSession = $oSession;
    }

    /**
     * Check if the JS donation box has to be added and redirect if the site hasn't been validated yet for a while.
     *
     * @return bool
     */
    public function needInject()
    {
        if (self::STATUS[mt_rand(0, count(self::STATUS) - 1)] === false) {
            return false;
        }

        $iSiteCreationDate = VDate::getTime(StatisticCoreModel::getDateOfCreation());

        if ($this->shouldUserBeRedirected($iSiteCreationDate)) {
            $this->redirectUserToDonationBox();
        }

        return $this->shouldUserSeeDialog();
    }

    /**
     * After over 2 months, if the site is still not validated, maybe the validation box doesn't really work...,
     * so we redirect directly to the page form.
     *
     * @param int $iSiteCreationDate
     *
     * @return bool
     */
    private function shouldUserBeRedirected($iSiteCreationDate)
    {
        return !$this->oValidateSiteModel->is() &&
            $this->removeTime(self::VALIDATE_FORM_PAGE_DELAY) >= $iSiteCreationDate &&
            !$this->oSession->exists(self::SESS_IS_VISITED);
    }

    /**
     * @param int $iSiteCreationDate
     *
     * @return bool
     */
    private function shouldUserSeeDialog($iSiteCreationDate)
    {
        $sTime = self::VALIDATE_FORM_POPUP_DELAYS[mt_rand(0, count(self::VALIDATE_FORM_POPUP_DELAYS) - 1)];

        return !$this->oValidateSiteModel->is() && $this->removeTime($sTime) >= $iSiteCreationDate;
    }

    private function redirectUserToDonationBox()
    {
        Header::redirect(
            Uri::get(
                'ph7cms-helper',
                'main',
                'suggestionbox',
                '?box=donationbox'
            )
        );
    }

    /**
     * @param string $sTime
     *
     * @return int The changed timestamp.
     */
    private function removeTime($sTime)
    {
        return VDate::setTime('-' . $sTime);
    }
}
