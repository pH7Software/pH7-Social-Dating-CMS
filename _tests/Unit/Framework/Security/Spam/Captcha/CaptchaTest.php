<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Spam / Captcha
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security\Spam\Captcha;

use ErrorException;
use PH7\Framework\Security\Spam\Captcha\Captcha;
use PH7\Framework\Session\Session;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CaptchaTest extends TestCase
{
    private ?string $sSessionPath = null;

    protected function tearDown(): void
    {
        if ($this->sSessionPath === null) {
            return;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            (new Session())->destroy();
        }
        foreach (glob($this->sSessionPath . '/*') as $sFile) {
            unlink($sFile);
        }
        rmdir($this->sSessionPath);
    }

    /**
     * A client that never requests the CAPTCHA image has no code in its session,
     * and must not be able to pass by submitting an empty answer.
     */
    public function testEmptyAnswerFailsWhenNoImageWasGenerated(): void
    {
        self::assertFalse($this->createCaptchaWithSessionCode(null)->check(''));
    }

    #[DataProvider('wrongAnswers')]
    public function testWrongAnswersFail(mixed $mAnswer): void
    {
        self::assertFalse($this->createCaptchaWithSessionCode('AbC12')->check($mAnswer));
    }

    public static function wrongAnswers(): array
    {
        return [[''], [null], ['AbC13'], ['abc12'], [['AbC12']]];
    }

    public function testCorrectAnswerPasses(): void
    {
        self::assertTrue($this->createCaptchaWithSessionCode('AbC12')->check('AbC12'));
    }

    public function testCaseInsensitiveAnswerPassesWhenAllowed(): void
    {
        self::assertTrue($this->createCaptchaWithSessionCode('AbC12')->check('abc12', false));
    }

    private function createCaptchaWithSessionCode(?string $sCode): Captcha
    {
        $this->sSessionPath = sys_get_temp_dir() . '/ph7-captcha-session-' . bin2hex(random_bytes(6));
        self::assertTrue(mkdir($this->sSessionPath, 0700));
        session_save_path($this->sSessionPath);

        $oCaptcha = new Captcha();
        $oSession = new Session();
        $oSession->remove('rand_code');
        if ($sCode !== null) {
            $oSession->set('rand_code', $sCode);
        }

        return $oCaptcha;
    }

    public function testTrueTypeRenderingDoesNotCoerceFractionalRandomBounds(): void
    {
        $oReflection = new ReflectionClass(Captcha::class);
        $oCaptcha = $oReflection->newInstanceWithoutConstructor();
        $oImage = imagecreate(160, 100);
        $iColor = imagecolorallocate($oImage, 0, 0, 0);

        foreach (
            [
                'rImg' => $oImage,
                'sStr' => 'A',
                'sFont' => PH7_PATH_PROTECTED . 'data/font/4.ttf',
                'iSize' => 35,
                'iMargin' => 25,
                'iStringWidth' => 20,
                'iHeight' => 40,
                'aColor' => [$iColor]
            ] as $sProperty => $mValue
        ) {
            $oReflection->getProperty($sProperty)->setValue($oCaptcha, $mValue);
        }

        $iPreviousErrorReporting = error_reporting(E_ALL);
        set_error_handler(
            static function (int $iSeverity, string $sMessage): bool {
                if ($iSeverity === E_DEPRECATED) {
                    throw new ErrorException($sMessage, 0, $iSeverity);
                }

                return false;
            }
        );

        try {
            $oReflection->getMethod('mixing')->invoke($oCaptcha, true);
            $this->addToAssertionCount(1);
        } finally {
            restore_error_handler();
            error_reporting($iPreviousErrorReporting);
        }
    }
}
