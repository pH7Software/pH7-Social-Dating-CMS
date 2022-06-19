<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Security\Security;
use PH7\Framework\Session\Session;
use stdClass;

class RememberMeCore
{
    public const CHECKBOX_FIELD_NAME = 'remember';
    public const STAY_LOGGED_IN_REQUESTED = 'stayed_logged_requested';
    public const DEFAULT_COOKIE_DURATION = 7776000; // 90 days

    private static int $iCookieDuration;

    public function __construct()
    {
        if (!isset(self::$iCookieDuration)) {
            $this->setRememberDuration(self::DEFAULT_COOKIE_DURATION);
        }
    }

    /**
     * @param int $iDurationSeconds Duration in seconds.
     */
    public function setRememberDuration(int $iDurationSeconds): void
    {
        self::$iCookieDuration = $iDurationSeconds;
    }

    public static function getRememberDurationInDays(): int
    {
        if (!isset(self::$iCookieDuration)) {
            self::$iCookieDuration = self::DEFAULT_COOKIE_DURATION;
        }

        return (int)ceil(self::$iCookieDuration / (3600 * 24));
    }

    public function isEligible(Session $oSession): bool
    {
        return $oSession->exists(self::STAY_LOGGED_IN_REQUESTED);
    }

    public function enableSession(stdClass $oUserData): void
    {
        $aCookieData = [
            // Hash one more time the password for the cookie
            'member_remember' => Security::hashCookie($oUserData->password),
            'member_id' => $oUserData->profileId
        ];
        (new Cookie)->set($aCookieData, null, self::$iCookieDuration);
    }
}
