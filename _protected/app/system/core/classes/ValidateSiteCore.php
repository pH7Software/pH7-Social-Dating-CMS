<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use
PH7\Framework\Url\Header,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Date\Various as VDate;

class ValidateSiteCore
{
    const SESS_IS_VISITED = 'validatesitebox_visited';

    /**
     * Check if the JS validationbox has to be added and redirect if the site hasn't been validated yet for a while.
     *
     * @param object \PH7\Framework\Session\Session $oSess
     * @return boolean
     */
    public static function needInject(Framework\Session\Session $oSess)
    {
        $oVSModel = new ValidateSiteCoreModel;
        $iSinceSiteCreated = VDate::getTime(StatisticCoreModel::getDateOfCreation());

        // After over 2 months, the site is still not validated, maybe the validation box doesn't really work, so we redirected to the page form
        if (!$oVSModel->is() && VDate::setTime('-2 months') >= $iSinceSiteCreated && !$oSess->exists(self::SESS_IS_VISITED)) {
            Header::redirect(Uri::get('validate-site', 'main', 'validationbox'));
        }

        if (!$oVSModel->is() && VDate::setTime('-2 days') >= $iSinceSiteCreated) {
            // OK for adding the validation colorbox
            return true;
        }
        return false;
    }
}
