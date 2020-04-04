<?php
/**
 * Miscellaneous Country Geo Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Geo / Misc
 */

namespace PH7\Framework\Geo\Misc;

defined('PH7') or exit('Restricted access');

class Country
{
    const UK_COUNTRY_CODE = 'UK';
    const GB_COUNTRY_CODE = 'GB';

    /**
     * pH7Framework uses UK instead of GB in its country details,
     * so we replace the wrong one to the correct one if found.
     *
     * @param string $sCountryCode Country code (e.g. GB, FR, US, ES, ...)
     *
     * @return string The correct country code.
     */
    public static function fixCode($sCountryCode)
    {
        return str_ireplace(self::GB_COUNTRY_CODE, self::UK_COUNTRY_CODE, $sCountryCode);
    }
}
