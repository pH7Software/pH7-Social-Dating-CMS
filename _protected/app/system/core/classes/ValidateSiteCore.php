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
        false
    ];

    const VALIDATE_FORM_POPUP_DELAYS = [
        '4 hours',
        '1 day',
        '3 days',
        '5 days',
        '10 days',
        '15 days',
        '21 days',
        '40 days'
    ];

    /**
     * Check if the JS donation box has to be added and redirect if the site hasn't been validated yet for a while.
     *
     * @param ValidateSiteCoreModel $oValidateSiteModel
     * @param Session $oSession
     *
     * @return bool
     */
    public static function needInject(ValidateSiteCoreModel $oValidateSiteModel, Session $oSession)
    {
        if (self::STATUS[mt_rand(0, count(self::STATUS) - 1)] === false) {
            return false;
        }

        $iSiteCreationDate = VDate::getTime(StatisticCoreModel::getDateOfCreation());

        if (self::shouldUserBeRedirected($iSiteCreationDate, $oValidateSiteModel, $oSession)) {
            Header::redirect(
                Uri::get(
                    'ph7cms-helper',
                    'main',
                    'suggestionbox',
                    '?box=donationbox'
                )
            );
        }

        $sTime = self::VALIDATE_FORM_POPUP_DELAYS[mt_rand(0, count(self::VALIDATE_FORM_POPUP_DELAYS) - 1)];

        return !$oValidateSiteModel->is() && self::removeTime($sTime) >= $iSiteCreationDate;
    }

    /**
     * After over 2 months, if the site is still not validated, maybe the validation box doesn't really work...,
     * so we redirect directly to the page form.
     *
     * @param int $iSiteCreationDate
     * @param ValidateSiteCoreModel $oValidateSiteModel
     * @param Session $oSession
     *
     * @return bool
     */
    private static function shouldUserBeRedirected(
        $iSiteCreationDate,
        ValidateSiteCoreModel $oValidateSiteModel,
        Session $oSession
    )
    {
        return !$oValidateSiteModel->is() &&
            self::removeTime(self::VALIDATE_FORM_PAGE_DELAY) >= $iSiteCreationDate &&
            !$oSession->exists(self::SESS_IS_VISITED);
    }

    /**
     * @param string $sTime
     *
     * @return int The changed timestamp.
     */
    private static function removeTime($sTime)
    {
        return VDate::setTime('-' . $sTime);
    }
}
