<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2021-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit
 */

declare(strict_types=1);

namespace PH7\Test\Unit;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\AssertionFailedError;
use ReflectionException;
use ReflectionProperty;

trait AssertionHelper
{
    public static function assertAttributeSame(
        $mExpected,
        string $sPropertyName,
        object $oInstance,
        string $sMessage = ''
    ): void {
        $mValue = self::getValue($oInstance, $sPropertyName);
        Assert::assertSame($mExpected, $mValue, $sMessage);
    }

    private static function getValue(object $oInstance, string $sPropertyName)
    {
        try {
            $oReflector = new ReflectionProperty($oInstance, $sPropertyName);
            $oReflector->setAccessible(true);
            $mValue = $oReflector->getValue($oInstance);
            $oReflector->setAccessible(false);

            return $mValue;
        } catch (ReflectionException $oExcept) {
            throw new AssertionFailedError($oExcept->getMessage());
        }
    }
}
