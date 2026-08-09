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
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CaptchaTest extends TestCase
{
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
