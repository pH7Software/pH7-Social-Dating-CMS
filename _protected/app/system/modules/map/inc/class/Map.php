<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Map / Inc / Class
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class Map
{
    const COUNTRY_CODE_LENGTH = 2;
    const MAX_COUNTRY_LENGTH = 50;
    const MAX_CITY_LENGTH = 50;

    /**
     * @param string $sCountryCode
     *
     * @return bool
     */
    public static function isCountryCodeTooLong($sCountryCode)
    {
        return strlen($sCountryCode) !== self::COUNTRY_CODE_LENGTH;
    }
}
