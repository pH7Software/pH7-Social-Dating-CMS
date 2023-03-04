<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2017-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

interface LoginableForm
{
    /**
     * Check if the existing password hash in the user record is outdated
     * with the current algorithm and password hashing options.
     * If so, we create and save the new password to match with the new algorithm and options.
     */
    public function updatePwdHashIfNeeded(string $sPassword, string $sUserPasswordHash, string $sEmail): void;

    /**
     * Enable the Captcha on the login form.
     */
    public function enableCaptcha(): void;
}
