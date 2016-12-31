<?php
/**
 * Miscellaneous Country Geo Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Geo / Misc
 */

namespace PH7\Framework\Geo\Misc;
defined('PH7') or exit('Restricted access');

class Country
{
    /**
     * pH7Framework uses UK instead of GB in its country details, so replace the wrong one if found.
     *
     * @param string $sCountryCode Country code (e.g. GB, FR, US, ES, ...)
     * @return string The correct country code.
     */
    public static function fixCode($sCountryCode)
    {
        return str_ireplace('GB', 'UK', $sCountryCode);
    }
}
