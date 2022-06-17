<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

declare(strict_types=1);

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
     * @return string
     */
    public static function username(string $sUsername, int $iMaxLength = PH7_MAX_USERNAME_LENGTH)
    {
        $sUsername = str_replace(['.', ' '], '-', $sUsername);

        return substr($sUsername, 0, $iMaxLength);
    }
}
