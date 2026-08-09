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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ValidateTest extends TestCase
{
    private Validate $oValidate;

    protected function setUp(): void
    {
        $this->oValidate = new Validate();
    }

    #[DataProvider('validHexCodesProvider')]
    public function testValidHexCode(string $sHexCode): void
    {
        $this->assertTrue($this->oValidate->hex($sHexCode));
    }

    #[DataProvider('invalidHexCodesProvider')]
    public function testInvalidHexCode(string $sHexCode): void
    {
        $this->assertFalse($this->oValidate->hex($sHexCode));
    }

    #[DataProvider('validNamesProvider')]
    public function testValidName(string $sName, int $iMinLength, int $iMaxLength): void
    {
        $this->assertTrue($this->oValidate->name($sName, $iMinLength, $iMaxLength));
    }

    #[DataProvider('invalidNamesProvider')]
    public function testInvalidName($mName, int $iMinLength, int $iMaxLength): void
    {
        $this->assertFalse($this->oValidate->name($mName, $iMinLength, $iMaxLength));
    }

    #[DataProvider('validPhoneNumbersProvider')]
    public function testValidPhoneNumber(string $sPhoneNumber): void
    {
        $this->assertSame(1, $this->oValidate->phone($sPhoneNumber));
    }

    #[DataProvider('invalidPhoneNumbersProvider')]
    public function testInvalidPhoneNumber(string $sPhoneNumber): void
    {
        $this->assertSame(0, $this->oValidate->phone($sPhoneNumber));
    }

    #[DataProvider('validPasswordsProvider')]
    public function testValidPassword(string $sPassword, int $iMinLength, int $iMaxLength): void
    {
        $this->assertTrue($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    #[DataProvider('invalidPasswordsProvider')]
    public function testInvalidPassword(string $sPassword, int $iMinLength, int $iMaxLength): void
    {
        $this->assertFalse($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    #[DataProvider('validIntegersProvider')]
    public function testValidInteger($mNumber): void
    {
        $this->assertTrue($this->oValidate->int($mNumber, 0, 60000));
    }

    #[DataProvider('invalidIntegersProvider')]
    public function testInvalidInteger($mNumber): void
    {
        $this->assertFalse($this->oValidate->int($mNumber, 0, 40000));
    }

    #[DataProvider('validUrlsProvider')]
    public function testValidUrl(string $sUrl): void
    {
        $this->assertTrue($this->oValidate->url($sUrl));
    }

    #[DataProvider('invalidUrlsProvider')]
    public function testInvalidUrl(string $sUrl): void
    {
        $this->assertFalse($this->oValidate->url($sUrl));
    }

    #[DataProvider('validFloatsProvider')]
    public function testValidFloat($fFloat): void
    {
        $this->assertTrue($this->oValidate->float($fFloat));
    }

    #[DataProvider('invalidFloatsProvider')]
    public function testInvalidFloat($mInvalidFloat): void
    {
        $this->assertFalse($this->oValidate->float($mInvalidFloat));
    }

    #[DataProvider('validDateOfBirthsProvider')]
    public function testBirthDate(string $sDate, int $iMinAge, int $iMaxAge): void
    {
        $this->assertTrue($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    #[DataProvider('invalidDateOfBirthsProvider')]
    public function testInvalidBirthDate(string $sDate, int $iMinAge, int $iMaxAge): void
    {
        $this->assertFalse($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    #[DataProvider('normalizedBirthDateProvider')]
    public function testBirthDateInputIsStrictlyNormalized(string $sInput, string $sExpected): void
    {
        $this->assertSame($sExpected, Validate::normalizeBirthDate($sInput));
    }

    public static function normalizedBirthDateProvider(): array
    {
        return [
            'HTML date input' => ['2000-02-29', '2000-02-29'],
            'legacy API date input' => ['02/29/2000', '2000-02-29'],
            'surrounding whitespace' => [' 2000-12-31 ', '2000-12-31']
        ];
    }

    #[DataProvider('invalidBirthDateInputProvider')]
    public function testInvalidBirthDateInputIsRejected(mixed $mInput): void
    {
        $this->assertNull(Validate::normalizeBirthDate($mInput));
    }

    public static function invalidBirthDateInputProvider(): array
    {
        return [
            'unparseable text' => ['not-a-date'],
            'impossible ISO date' => ['2001-02-29'],
            'impossible legacy date' => ['02/30/2000'],
            'ambiguous format' => ['31/12/2000'],
            'missing zero padding' => ['2000-2-29'],
            'timestamp' => ['951782400'],
            'date with time' => ['2000-02-29T00:00:00Z'],
            'array-shaped input' => [['2000-02-29']]
        ];
    }

    public static function validHexCodesProvider(): array
    {
        return [
            ['#eee'],
            ['#EEE'],
            ['#eeeeee']
        ];
    }

    public static function invalidHexCodesProvider(): array
    {
        return [
            ['eee'],
            ['#fffffff'],
            ['#cc']
        ];
    }

    public static function validNamesProvider(): array
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

    public static function invalidNamesProvider(): array
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

    public static function validPhoneNumbersProvider(): array
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

    public static function invalidPhoneNumbersProvider(): array
    {
        return [
            ['++0768374890'],
            ['0485'],
            ['zerozerozero'],
            ['']
        ];
    }

    public static function validPasswordsProvider(): array
    {
        return [
            ['8374878*&@*#*5r8hjvfj^', 2, 40],
            ['12345678', 4, 8]
        ];
    }

    public static function invalidPasswordsProvider(): array
    {
        return [
            ['1234567', 10, 30],
            ['itititkfjgk9*(&$*#&*(8342', 5, 10],
            ['', 6, 40]
        ];
    }

    public static function validIntegersProvider(): array
    {
        return [
            [1],
            [59868],
            [0],
            ['34']
        ];
    }

    public static function invalidIntegersProvider(): array
    {
        return [
            ['one'],
            ['lalal'],
            [''],
            [50000] // Exceed the maximum value set (max_range)
        ];
    }

    public static function validUrlsProvider(): array
    {
        return [
            ['http://example.com'],
            ['https://ph7builder.com'],
            ['https://sub.example.com/path?q=1&x=2#frag']
        ];
    }

    public static function invalidUrlsProvider(): array
    {
        return [
            // Script/scheme-injection vectors that FILTER_VALIDATE_URL alone lets through
            'javascript scheme' => ['javascript://alert(1)'],
            'non-http scheme' => ['ftp://files.example.com'],
            // Plainly malformed
            'no scheme' => ['example.com'],
            'empty' => ['']
        ];
    }

    public static function validFloatsProvider(): array
    {
        return [
            [1.5],
            [0.54532],
            [0.0],
            ['3.0']
        ];
    }

    public static function invalidFloatsProvider(): array
    {
        return [
            ['one'],
            ['lalal'],
            [''],
            [null]
        ];
    }

    public static function validDateOfBirthsProvider(): array
    {
        return [
            ['02/02/1989', 18, 99],
            ['1989-02-02', 18, 99],
            ['02/22/1990', 20, 90],
            ['12/10/1998', 18, 80],
            ['12/10/1998', 18, 99]
        ];
    }

    public static function invalidDateOfBirthsProvider(): array
    {
        return [
            ['00/00/0000', 18, 99],
            ['01/10/1980', 18, 20],
            ['01/03/01990', 18, 99],
            ['01/03/1990/03', 18, 99],
            ['03/00/1986', 18, 99],
            ['03-10-1986', 18, 99],
            ['not-a-date', 18, 99],
            ['2001-02-29', 18, 99],
            ['02/30/2000', 18, 99],
        ];
    }
}
