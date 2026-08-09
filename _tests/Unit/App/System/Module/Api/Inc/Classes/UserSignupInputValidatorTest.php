<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Api\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'api/inc/class/UserSignupInputValidator.php';

use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Str\Str;
use PH7\UserSignupInputValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserSignupInputValidatorTest extends TestCase
{
    private UserSignupInputValidator $oValidator;

    protected function setUp(): void
    {
        $this->oValidator = new UserSignupInputValidator(new Validate());
    }

    public function testValidProfileFieldsAreAccepted(): void
    {
        $this->assertTrue($this->oValidator->isValid(self::validProfileData(), ['AU', 'US']));
    }

    public function testPersistedPlainTextKeepsValidatedDatabaseLengths(): void
    {
        $aData = self::validProfileData();
        $aData['city'] = str_repeat('&', 150);
        $aData['description'] = str_repeat('&', 4000);

        $this->assertTrue($this->oValidator->isValid($aData, ['AU', 'US']));

        $aPersistedData = (new Str())->escape($aData, true);

        $this->assertSame($aData['city'], $aPersistedData['city']);
        $this->assertSame($aData['description'], $aPersistedData['description']);
        $this->assertSame(150, mb_strlen($aPersistedData['city']));
        $this->assertSame(4000, mb_strlen($aPersistedData['description']));
    }

    #[DataProvider('invalidProfileFieldProvider')]
    public function testInvalidProfileFieldsAreRejected(string $sField, mixed $mValue): void
    {
        $aData = self::validProfileData();
        $aData[$sField] = $mValue;

        $this->assertFalse($this->oValidator->isValid($aData, ['AU', 'US']));
    }

    public static function invalidProfileFieldProvider(): array
    {
        return [
            'short first name' => ['first_name', 'A'],
            'invalid last name' => ['last_name', 'Smith2'],
            'invalid gender' => ['sex', 'invalid'],
            'empty match genders' => ['match_sex', []],
            'invalid match gender' => ['match_sex', ['invalid']],
            'unknown country' => ['country', 'FR'],
            'oversized country' => ['country', 'USA'],
            'short city' => ['city', 'A'],
            'oversized city' => ['city', str_repeat('C', 151)],
            'short state' => ['state', 'A'],
            'oversized state' => ['state', str_repeat('S', 151)],
            'short postal code' => ['zip_code', '1'],
            'oversized postal code' => ['zip_code', str_repeat('1', 16)],
            'short description' => ['description', str_repeat('D', 19)],
            'oversized description' => ['description', str_repeat('D', 4001)]
        ];
    }

    private static function validProfileData(): array
    {
        return [
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'sex' => 'female',
            'match_sex' => ['male', 'female'],
            'country' => 'AU',
            'city' => 'Brisbane',
            'state' => 'Queensland',
            'zip_code' => '4000',
            'description' => 'A complete profile description.'
        ];
    }
}
