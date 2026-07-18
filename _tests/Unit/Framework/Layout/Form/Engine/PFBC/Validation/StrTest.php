<?php

/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC / Validation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC\Validation;

// PFBC registers its own spl_autoload_register in Form.class.php
require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

use PFBC\Validation\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class StrTest extends TestCase
{
    #[DataProvider('maxProvider')]
    public function testGetMaxReturnsTheDeclaredMaximum(?int $iMin, ?int $iMax, ?int $iExpected): void
    {
        $oValidation = new Str($iMin, $iMax);

        $this->assertSame($iExpected, $oValidation->getMax());
    }

    public static function maxProvider(): array
    {
        return [
            'min and max' => [10, 2000, 2000],
            'only min (unbounded max)' => [4, null, null],
            'no bounds' => [null, null, null],
            'zero max stays null (empty)' => [1, 0, null],
        ];
    }

    public function testWithinBoundsIsValid(): void
    {
        $oValidation = new Str(3, 10);

        $this->assertTrue($oValidation->isValid('hello'));
    }

    public function testTooLongIsInvalid(): void
    {
        $oValidation = new Str(3, 5);

        $this->assertFalse($oValidation->isValid('way too long'));
    }

    public function testTooShortIsInvalid(): void
    {
        $oValidation = new Str(5, 100);

        $this->assertFalse($oValidation->isValid('hi'));
    }
}
