<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Profile Faker
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\ProfileFaker;

use PH7\GenerateProfileForm;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class GenerateProfileFormSafetyTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        require_once PH7_PATH_SYS_MOD . 'profile-faker/forms/GenerateProfileForm.php';
    }

    #[DataProvider('amountLimitProvider')]
    public function testProfileGenerationBatchAmountIsBounded(int $iAmount, bool $bExpected): void
    {
        self::assertSame($bExpected, GenerateProfileForm::isValidAmount($iAmount));
    }

    public static function amountLimitProvider(): iterable
    {
        yield 'below minimum' => [0, false];
        yield 'minimum' => [1, true];
        yield 'default' => [20, true];
        yield 'maximum' => [100, true];
        yield 'above maximum' => [101, false];
    }

    public function testProfileGenerationBatchIsBoundedInTheFormAndOnTheServer(): void
    {
        $sFormPath = PH7_PATH_SYS_MOD . 'profile-faker/forms/GenerateProfileForm.php';
        $sProcessorPath = PH7_PATH_SYS_MOD . 'profile-faker/forms/processing/GenerateProfileFormProcess.php';
        $sForm = file_get_contents($sFormPath);
        $sProcessor = file_get_contents($sProcessorPath);

        self::assertIsString($sForm);
        self::assertIsString($sProcessor);
        self::assertStringContainsString("'min' => self::MIN_AMOUNT_VALUE", $sForm);
        self::assertStringContainsString('public const MAX_AMOUNT_VALUE = 100;', $sForm);
        self::assertStringContainsString("'max' => self::MAX_AMOUNT_VALUE", $sForm);
        self::assertStringContainsString('GenerateProfileForm::isValidAmount($iAmountProfiles)', $sProcessor);
        self::assertStringContainsString("\\PFBC\\Form::setError(", $sProcessor);
    }
}
