<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

declare(strict_types=1);

namespace PH7;

interface Authenticable
{
    /**
     * Determines if the captcha is eligible or not.
     *
     * @return bool
     */
    public static function isCaptchaEligible(): bool;

    /**
     * Remove the session if the affiliate is logged on as "user" or "affiliate".
     *
     * @return void
     */
    public static function clearCurrentSessions(): void;
}
