<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

interface LoginableForm
{
    /**
     * Check if the existing password hash in the user record is outdated
     * with the current algorithm and password hashing options.
     * If so, we create and save the new password to match with the new algorithm and options.
     *
     * @param string $sPassword
     * @param string $sUserPasswordHash
     * @param string $sEmail
     *
     * @return void
     */
    public function updatePwdHashIfNeeded($sPassword, $sUserPasswordHash, $sEmail);

    /**
     * Enable the Captcha on the login form.
     *
     * @return void
     */
    public function enableCaptcha();
}
