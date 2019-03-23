<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

namespace PH7;

use PH7\Framework\Pattern\Statik;

class Cleanup
{
    /**
     * Import the trait to set the class static.
     *
     * The trait sets constructor & cloning private to prevent instantiation.
     */
    use Statik;

    /**
     * Remove invalid characters that may contain in the Faker usernames.
     *
     * @param string $sUsername
     * @param int $iMaxLength
     *
     * @return string
     */
    public static function username($sUsername, $iMaxLength = PH7_MAX_USERNAME_LENGTH)
    {
        $sUsername = str_replace(['.', ' '], '-', $sUsername);

        return substr($sUsername, 0, $iMaxLength);
    }
}
