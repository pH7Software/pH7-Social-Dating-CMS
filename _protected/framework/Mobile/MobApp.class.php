<?php
/**
 * @title            Mobile App class for iOS/Android apps.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mobile
 */

namespace PH7\Framework\Mobile;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

class MobApp
{

    // Request name used in mobile apps
    const REQ_NAME = 'mobapp';

    /**
     * Check if a mobile native app called the site.
     *
     * @return boolean
     */
    final public static function is()
    {
        return (new Http)->getExists(static::REQ_NAME);
    }

}
