<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\SmsVerification\Inc\Classes;

use PH7\Framework\Config\Config;
use PH7\Framework\Session\Session;
use PH7\SmsVerificationCore;
use PH7\Verification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class VerificationTest extends TestCase
{
    private Session $oSession;

    protected function setUp(): void
    {
        require_once PH7_PATH_SYS_MOD . 'sms-verification/inc/class/Verification.php';
        $this->oSession = (new ReflectionClass(Session::class))->newInstanceWithoutConstructor();
        $_SESSION = [];
        Config::getInstance()->values['module.setting']['verification_code.length'] = 6;
        SmsVerificationCore::beginChallenge($this->oSession, 42);
    }

    #[DataProvider('provideLengths')]
    public function testIssuedCodeMatchesTheFormAndCanOnlyBeUsedOnce(int $iLength): void
    {
        Config::getInstance()->values['module.setting']['verification_code.length'] = $iLength;
        $sCode = Verification::issueCode($this->oSession, '+12025550123');
        self::assertMatchesRegularExpression('/^\d{' . $iLength . '}$/D', $sCode);
        self::assertSame($iLength, Verification::getCodeLength());
        self::assertTrue(Verification::consumeCode($this->oSession, $sCode));
        self::assertFalse(Verification::consumeCode($this->oSession, $sCode));
        self::assertSame('+12025550123', $this->oSession->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
    }

    public static function provideLengths(): array
    {
        return [[4], [6], [8]];
    }

    public function testFiveFailedAttemptsInvalidateTheCode(): void
    {
        $sCode = Verification::issueCode($this->oSession, '+12025550123');
        for ($iAttempt = 0; $iAttempt < 5; ++$iAttempt) {
            self::assertFalse(Verification::consumeCode($this->oSession, 'wrong'));
        }
        self::assertFalse(Verification::consumeCode($this->oSession, $sCode));
    }

    public function testExpiredCodeIsRejected(): void
    {
        $sCode = Verification::issueCode($this->oSession, '+12025550123');
        $aCode = $this->oSession->get(SmsVerificationCore::CODE_SESS_NAME);
        $aCode['expires'] = time() - 1;
        $this->oSession->set(SmsVerificationCore::CODE_SESS_NAME, $aCode);
        self::assertFalse(Verification::consumeCode($this->oSession, $sCode));
    }

    public function testExpiredChallengeCannotIssueOrConsumeCodes(): void
    {
        $sCode = Verification::issueCode($this->oSession, '+12025550123');
        $this->oSession->set('sms_verification_expires', time() - 1);
        self::assertNull(SmsVerificationCore::getChallengeProfileId($this->oSession));
        self::assertNull(Verification::issueCode($this->oSession, '+12025550123'));
        self::assertFalse(Verification::consumeCode($this->oSession, $sCode));
    }

    public function testLegacyChallengeMustRestart(): void
    {
        SmsVerificationCore::clearChallenge($this->oSession);
        $this->oSession->set(SmsVerificationCore::PROFILE_ID_SESS_NAME, 42);
        self::assertNull(SmsVerificationCore::getChallengeProfileId($this->oSession));
        self::assertNull(Verification::issueCode($this->oSession, '+12025550123'));
    }

    public function testResendsHaveACooldownAndReplaceThePreviousCode(): void
    {
        $sOldCode = Verification::issueCode($this->oSession, '+12025550123');
        self::assertNull(Verification::issueCode($this->oSession, '+12025550124'));
        self::assertSame('+12025550123', $this->oSession->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
        $this->allowNextSend();
        $sNewCode = Verification::issueCode($this->oSession, '+12025550124');
        self::assertIsString($sNewCode);
        self::assertSame('+12025550124', $this->oSession->get(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
        if ($sOldCode !== $sNewCode) {
            self::assertFalse(Verification::consumeCode($this->oSession, $sOldCode));
        }
        self::assertTrue(Verification::consumeCode($this->oSession, $sNewCode));
    }

    public function testSendLimitSurvivesRestartingTheLoginChallenge(): void
    {
        for ($iSend = 0; $iSend < 5; ++$iSend) {
            self::assertIsString(Verification::issueCode($this->oSession, '+12025550123'));
            $this->allowNextSend();
        }
        SmsVerificationCore::beginChallenge($this->oSession, 42);
        self::assertNull(Verification::issueCode($this->oSession, '+12025550123'));
        $aRate = $this->oSession->get('sms_verification_rate');
        $aRate['started_at'] = time() - 901;
        $this->oSession->set('sms_verification_rate', $aRate);
        self::assertIsString(Verification::issueCode($this->oSession, '+12025550123'));
    }

    public function testChangingProfilesClearsThePreviousVerification(): void
    {
        $sCode = Verification::issueCode($this->oSession, '+12025550123');
        SmsVerificationCore::beginChallenge($this->oSession, 43);
        self::assertSame(43, SmsVerificationCore::getChallengeProfileId($this->oSession));
        self::assertFalse($this->oSession->exists(SmsVerificationCore::PHONE_NUMBER_SESS_NAME));
        self::assertFalse(Verification::consumeCode($this->oSession, $sCode));
    }

    #[DataProvider('provideInvalidLengths')]
    public function testUnsafeCodeLengthsAreRejected(int $iLength): void
    {
        Config::getInstance()->values['module.setting']['verification_code.length'] = $iLength;
        $this->expectException(\InvalidArgumentException::class);
        Verification::issueCode($this->oSession, '+12025550123');
    }

    public static function provideInvalidLengths(): array
    {
        return [[0], [3], [9], [1000]];
    }

    private function allowNextSend(): void
    {
        $aRate = $this->oSession->get('sms_verification_rate');
        $aRate['last_sent'] = time() - 61;
        $this->oSession->set('sms_verification_rate', $aRate);
    }
}
