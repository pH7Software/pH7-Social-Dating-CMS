<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Field / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Field\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'field/inc/class/Field.php';

use PH7\Field;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FieldTest extends TestCase
{
    const PHONE_FIELD_NAME = 'phone';
    const MIDDLE_NAME_FIELD_NAME = 'middleName';
    const PUNCHLINE_FIELD_NAME = 'punchline';
    const CUSTOM_FIELD_NAME = 'myownfield';

    public function testUserTable(): void
    {
        $sResult = Field::getTable('user');

        $this->assertSame('members_info', $sResult);
    }

    public function testAffiliateTable(): void
    {
        $sResult = Field::getTable('aff');

        $this->assertSame('affiliates_info', $sResult);
    }

    public function testUserModifiableField(): void
    {
        $bResult = Field::unmodifiable('user', self::CUSTOM_FIELD_NAME);

        $this->assertFalse($bResult);
    }

    public function testAffiliateModifiableField(): void
    {
        $bResult = Field::unmodifiable('aff', self::PUNCHLINE_FIELD_NAME);

        $this->assertFalse($bResult);
    }

    public function testUserUnmodifiableField(): void
    {
        $bResult = Field::unmodifiable('user', self::PUNCHLINE_FIELD_NAME);

        $this->assertTrue($bResult);
    }

    public function testAffiliateUnmodifiableField(): void
    {
        $bResult = Field::unmodifiable('aff', self::PHONE_FIELD_NAME);

        $this->assertTrue($bResult);
    }

    public function testAffiliateMiddleNameFieldIsAlwaysProtected(): void
    {
        $bResult = Field::unmodifiable('aff', self::MIDDLE_NAME_FIELD_NAME);

        $this->assertTrue($bResult);
    }

    public function testUserPhoneFieldIsAlwaysProtected(): void
    {
        $bResult = Field::unmodifiable('user', self::PHONE_FIELD_NAME);

        $this->assertTrue($bResult);
    }

    #[DataProvider('hardCodedFieldProvider')]
    public function testHardCodedSystemFieldsAreAlwaysProtected(string $sMod, string $sField): void
    {
        $this->assertTrue(Field::unmodifiable($sMod, $sField));
    }

    public static function hardCodedFieldProvider(): array
    {
        return [
            'member profile ID' => ['user', 'profileId'],
            'member middle name' => ['user', 'middleName'],
            'member description' => ['user', 'description'],
            'member punchline' => ['user', 'punchline'],
            'member city' => ['user', 'city'],
            'member state' => ['user', 'state'],
            'member postal code' => ['user', 'zipCode'],
            'member country' => ['user', 'country'],
            'member phone' => ['user', 'phone'],
            'member height' => ['user', 'height'],
            'member weight' => ['user', 'weight'],
            'affiliate profile ID' => ['aff', 'profileId'],
            'affiliate middle name' => ['aff', 'middleName'],
            'affiliate description' => ['aff', 'description'],
            'affiliate address' => ['aff', 'address'],
            'affiliate phone' => ['aff', 'phone'],
            'affiliate city' => ['aff', 'city'],
            'affiliate state' => ['aff', 'state'],
            'affiliate postal code' => ['aff', 'zipCode'],
            'affiliate country' => ['aff', 'country'],
            'affiliate website' => ['aff', 'website']
        ];
    }
}
