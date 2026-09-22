<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Form / Engine / PFBC
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Form\Engine\PFBC;

use PFBC\Validation\Date;
use PFBC\Validation\Str;
use PFBC\Validation\Username;
use PH7\DbTableName;
use PH7\Framework\Security\Validate\Validate;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;

require_once PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/PFBC/Form.class.php';

/**
 * A field missing from the request, such as a disabled input, reaches its validation rules as
 * null. Passing that null to string functions is deprecated, and becomes a TypeError in PHP 9.
 */
final class ValidationMissingValueTest extends TestCase
{
    public function testLengthRuleAcceptsAMissingOptionalField(): void
    {
        $this->assertValidationWithoutDeprecation(true, static fn (): bool => (new Str(2, 10))->isValid(null));
    }

    public function testLengthRuleStillChecksWhatWasSent(): void
    {
        $oRule = new Str(2, 10);

        self::assertTrue($oRule->isValid('   '));
        self::assertFalse($oRule->isValid(' a '));
        self::assertTrue($oRule->isValid(' ab '));
    }

    public function testDateRuleAcceptsAMissingOptionalField(): void
    {
        $this->assertValidationWithoutDeprecation(true, static fn (): bool => (new Date)->isValid(null));
    }

    public function testDateRuleStillChecksWhatWasSent(): void
    {
        self::assertTrue((new Date)->isValid('2026-09-22'));
        self::assertFalse((new Date)->isValid('not a date'));
    }

    public function testUsernameRuleRejectsAMissingUsername(): void
    {
        // The app's configs/constants.php defines this; the test bootstrap does not load it.
        if (!defined('PH7_USERNAME_PATTERN')) {
            define('PH7_USERNAME_PATTERN', '[\w-]');
        }

        // Built without its constructor, which reads the length settings from the database.
        $oRule = (new ReflectionClass(Username::class))->newInstanceWithoutConstructor();
        $aProperties = ['oValidate' => new Validate, 'sTable' => DbTableName::ADMIN, 'iMin' => 3, 'iMax' => 30];
        foreach ($aProperties as $sProperty => $mValue) {
            (new ReflectionProperty($oRule, $sProperty))->setValue($oRule, $mValue);
        }

        $this->assertValidationWithoutDeprecation(false, static fn (): bool => $oRule->isValid(null));
    }

    private function assertValidationWithoutDeprecation(bool $bExpected, callable $fnValidate): void
    {
        $aDeprecations = [];
        set_error_handler(static function (int $iLevel, string $sMessage) use (&$aDeprecations): bool {
            $aDeprecations[] = $sMessage;

            return true;
        }, E_DEPRECATED);

        try {
            self::assertSame($bExpected, $fnValidate());
        } finally {
            restore_error_handler();
        }

        self::assertSame([], $aDeprecations);
    }
}
