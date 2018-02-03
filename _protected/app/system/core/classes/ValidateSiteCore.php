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
    const SESS_IS_VISITED = 'donationbox_visited';
    const VALIDATE_FORM_PAGE_DELAY = '-2 months';
    const VALIDATE_FORM_POPUP_DELAY = '-4 days';

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
        $iSinceSiteCreated = VDate::getTime(StatisticCoreModel::getDateOfCreation());

        // After over 2 months, if the site is still not validated, maybe the validation box doesn't really work...,
        // so we redirect directly to the page form
        if (
            !$oValidateSiteModel->is() && VDate::setTime(self::VALIDATE_FORM_PAGE_DELAY) >= $iSinceSiteCreated &&
            !$oSession->exists(self::SESS_IS_VISITED)
        ) {
            Header::redirect(
                Uri::get('ph7cms-donation', 'main', 'donationbox')
            );
        }

        return !$oValidateSiteModel->is() && VDate::setTime(self::VALIDATE_FORM_POPUP_DELAY) >= $iSinceSiteCreated;
    }
}
