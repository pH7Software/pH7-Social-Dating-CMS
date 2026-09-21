<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Config\Config;
use PH7\Framework\Pattern\Statik;
use PH7\Framework\Session\Session;

class Verification
{
    /**
     * Sets constructor/clone private to prevent instantiation, since it's a static class.
     */
    use Statik;
    private const CODE_LIFETIME = 300;
    private const MAX_ATTEMPTS = 5;
    private const RESEND_DELAY = 60;
    private const SEND_WINDOW = 900;
    private const MAX_SENDS = 5;
    private const RATE_SESS_NAME = 'sms_verification_rate';

    public static function getCodeLength(): int
    {
        $iLength = (int)Config::getInstance()->values['module.setting']['verification_code.length'];
        if ($iLength < 4 || $iLength > 8) {
            throw new \InvalidArgumentException('SMS verification code length must be between 4 and 8.');
        }

        return $iLength;
    }

    public static function issueCode(Session $oSession, string $sPhoneNumber): ?string
    {
        if (SmsVerificationCore::getChallengeProfileId($oSession) === null || $sPhoneNumber === '') {
            return null;
        }

        $aRate = $oSession->get(self::RATE_SESS_NAME);
        if (!is_array($aRate) || $aRate['started_at'] + self::SEND_WINDOW <= time()) {
            $aRate = ['started_at' => time(), 'last_sent' => 0, 'count' => 0];
        }
        if ($aRate['count'] >= self::MAX_SENDS || $aRate['last_sent'] + self::RESEND_DELAY > time()) {
            return null;
        }

        $iLength = self::getCodeLength();
        $sCode = str_pad((string)random_int(0, 10 ** $iLength - 1), $iLength, '0', STR_PAD_LEFT);
        ++$aRate['count'];
        $aRate['last_sent'] = time();
        $oSession->set([
            self::RATE_SESS_NAME => $aRate,
            SmsVerificationCore::PHONE_NUMBER_SESS_NAME => $sPhoneNumber,
            SmsVerificationCore::CODE_SESS_NAME => [
                'hash' => hash('sha256', $sCode),
                'expires' => time() + self::CODE_LIFETIME,
                'attempts' => 0
            ]
        ]);

        return $sCode;
    }

    public static function consumeCode(Session $oSession, string $sCode): bool
    {
        $aCode = $oSession->get(SmsVerificationCore::CODE_SESS_NAME);
        if (SmsVerificationCore::getChallengeProfileId($oSession) === null
            || !is_array($aCode) || $aCode['expires'] <= time() || $aCode['attempts'] >= self::MAX_ATTEMPTS) {
            return false;
        }

        ++$aCode['attempts'];
        $oSession->set(SmsVerificationCore::CODE_SESS_NAME, $aCode);
        if (!hash_equals($aCode['hash'], hash('sha256', $sCode))) {
            return false;
        }

        $oSession->remove(SmsVerificationCore::CODE_SESS_NAME);

        return true;
    }
}
