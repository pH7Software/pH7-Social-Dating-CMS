<?php
/**
 * @title            Mobile App class for iOS/Android apps.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mobile
 */

namespace PH7\Framework\Mobile;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Session\Session;

class MobApp
{
    // Request name used in mobile apps
    const VAR_NAME = 'mobapp';

    /**
     * Check if a mobile native app called the site.
     *
     * @param Http $oHttp
     * @param Session $oSession
     *
     * @return bool
     */
    final public static function is(Http $oHttp, Session $oSession)
    {
        if ($oHttp->getExists(static::VAR_NAME)) {
            $oSession->set(static::VAR_NAME, 1);
        }

        return $oSession->exists(static::VAR_NAME);
    }
}
