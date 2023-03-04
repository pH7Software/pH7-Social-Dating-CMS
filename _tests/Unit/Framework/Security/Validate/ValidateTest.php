<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Validate
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security\Validate;

use PH7\Framework\Security\Validate\Validate;
use PHPUnit\Framework\TestCase;

final class ValidateTest extends TestCase
{
    private Validate $oValidate;

    protected function setUp(): void
    {
        $this->oValidate = new Validate();
    }

    /**
     * @dataProvider validHexCodesProvider
     */
    public function testValidHexCode(string $sHexCode): void
    {
        $this->assertTrue($this->oValidate->hex($sHexCode));
    }

    /**
     * @dataProvider invalidHexCodesProvider
     */
    public function testInvalidHexCode(string $sHexCode): void
    {
        $this->assertFalse($this->oValidate->hex($sHexCode));
    }

    /**
     * @dataProvider validNamesProvider
     */
    public function testValidName(string $sName, int $iMinLength, int $iMaxLength): void
    {
        $this->assertTrue($this->oValidate->name($sName, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider invalidNamesProvider
     */
    public function testInvalidName($mName, int $iMinLength, int $iMaxLength): void
    {
        $this->assertFalse($this->oValidate->name($mName, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider validPhoneNumbersProvider
     */
    public function testValidPhoneNumber(string $sPhoneNumber): void
    {
        $this->assertSame(1, $this->oValidate->phone($sPhoneNumber));
    }

    /**
     * @dataProvider invalidPhoneNumbersProvider
     */
    public function testInvalidPhoneNumber(string $sPhoneNumber): void
    {
        $this->assertSame(0, $this->oValidate->phone($sPhoneNumber));
    }

    /**
     * @dataProvider validPasswordsProvider
     */
    public function testValidPassword(string $sPassword, int $iMinLength, int $iMaxLength): void
    {
        $this->assertTrue($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider invalidPasswordsProvider
     */
    public function testInvalidPassword(string $sPassword, int $iMinLength, int $iMaxLength): void
    {
        $this->assertFalse($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider validIntegersProvider
     */
    public function testValidInteger($mNumber): void
    {
        $this->assertTrue($this->oValidate->int($mNumber, 0, 60000));
    }

    /**
     * @dataProvider invalidIntegersProvider
     */
    public function testInvalidInteger($mNumber): void
    {
        $this->assertFalse($this->oValidate->int($mNumber, 0, 40000));
    }

    /**
     * @dataProvider validFloatsProvider
     */
    public function testValidFloat($fFloat): void
    {
        $this->assertTrue($this->oValidate->float($fFloat));
    }

    /**
     * @dataProvider invalidFloatsProvider
     */
    public function testInvalidFloat($mInvalidFloat): void
    {
        $this->assertFalse($this->oValidate->float($mInvalidFloat));
    }

    /**
     * @dataProvider validDateOfBirthsProvider
     */
    public function testBirthDate(string $sDate, int $iMinAge, int $iMaxAge): void
    {
        $this->assertTrue($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    /**
     * @dataProvider invalidDateOfBirthsProvider
     */
    public function testInvalidBirthDate(string $sDate, int $iMinAge, int $iMaxAge): void
    {
        $this->assertFalse($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    public function validHexCodesProvider(): array
    {
        return [
            ['#eee'],
            ['#EEE'],
            ['#eeeeee']
        ];
    }

    public function invalidHexCodesProvider(): array
    {
        return [
            ['eee'],
            ['#fffffff'],
            ['#cc']
        ];
    }

    public function validNamesProvider(): array
    {
        return [
            ['Píėrre', 2, 20],
            ['Amélie', 2, 20],
            ['Pierre-Henry', 2, 20],
            ['Pierre-Henry Soria', 2, 20],
            ['Pierre-Théodore Rollier', 10, 25],
            ['Àngel Nøisã', 2, 20],
            ['Nôël Großkreutz', 2, 20],
            ['Camarón de la Isla', 2, 20]
        ];
    }

    public function invalidNamesProvider(): array
    {
        return [
            ['abcdef', 2, 4],
            ['o', 2, 20],
            ['{NOT A NAME}', 2, 20],
            ['*&^', 2, 20],
            ['http://affiliate-site.com', 2, 20],
            ['https://spam', 2, 20],
            [4335, 2, 20],
            ['$money$', 2, 20],
            ['James€', 2, 20]
        ];
    }

    public function validPhoneNumbersProvider(): array
    {
        return [
            ['+44768374890'],
            ['+41446681810'],
            ['0041446681810'],
            ['+336123436489'],
            ['+16502530001'],
            ['0016502530001']
        ];
    }

    public function invalidPhoneNumbersProvider(): array
    {
        return [
            ['++0768374890'],
            ['0485'],
            ['zerozerozero'],
            ['']
        ];
    }

    public function validPasswordsProvider(): array
    {
        return [
            ['8374878*&@*#*5r8hjvfj^', 2, 40],
            ['12345678', 4, 8]
        ];
    }

    public function invalidPasswordsProvider(): array
    {
        return [
            ['1234567', 10, 30],
            ['itititkfjgk9*(&$*#&*(8342', 5, 10],
            ['', 6, 40]
        ];
    }

    public function validIntegersProvider(): array
    {
        return [
            [1],
            [59868],
            [0],
            ['34']
        ];
    }

    public function invalidIntegersProvider(): array
    {
        return [
            ['one'],
            ['lalal'],
            [''],
            [50000] // Exceed the maximum value set (max_range)
        ];
    }

    public function validFloatsProvider(): array
    {
        return [
            [1.5],
            [0.54532],
            [0.0],
            ['3.0']
        ];
    }

    public function invalidFloatsProvider(): array
    {
        return [
            ['one'],
            ['lalal'],
            [''],
            [null]
        ];
    }

    public function validDateOfBirthsProvider(): array
    {
        return [
            ['02/02/1989', 18, 99],
            ['02/22/1990', 20, 90],
            ['12/10/1998', 18, 80],
            ['12/10/1998', 18, 99]
        ];
    }

    public function invalidDateOfBirthsProvider(): array
    {
        return [
            ['00/00/0000', 18, 99],
            ['01/10/1980', 18, 20],
            ['01/03/01990', 18, 99],
            ['01/03/1990/03', 18, 99],
            ['03/00/1986', 18, 99],
            ['03-10-1986', 18, 99],
        ];
    }
}
