<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Validate
 */

namespace PH7\Test\Unit\Framework\Security\Validate;

use PH7\Framework\Security\Validate\Validate;
use PHPUnit_Framework_TestCase;

class ValidateTest extends PHPUnit_Framework_TestCase
{
    /** @var Validate */
    private $oValidate;

    protected function setUp()
    {
        $this->oValidate = new Validate();
    }

    /**
     * @dataProvider validHexCodesProvider
     */
    public function testValidHexCode($sHexCode)
    {
        $this->assertTrue($this->oValidate->hex($sHexCode));
    }

    /**
     * @dataProvider invalidHexCodesProvider
     */
    public function testInvalidHexCode($sHexCode)
    {
        $this->assertFalse($this->oValidate->hex($sHexCode));
    }

    /**
     * @dataProvider validNamesProvider
     */
    public function testValidName($sName, $iMinLength, $iMaxLength)
    {
        $this->assertTrue($this->oValidate->name($sName, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider invalidNamesProvider
     */
    public function testInvalidName($sName, $iMinLength, $iMaxLength)
    {
        $this->assertFalse($this->oValidate->name($sName, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider validPhoneNumbersProvider
     */
    public function testValidPhoneNumber($sPhoneNumber)
    {
        $this->assertSame(1, $this->oValidate->phone($sPhoneNumber));
    }

    /**
     * @dataProvider invalidPhoneNumbersProvider
     */
    public function testInvalidPhoneNumber($sPhoneNumber)
    {
        $this->assertSame(0, $this->oValidate->phone($sPhoneNumber));
    }

    /**
     * @dataProvider validPasswordsProvider
     */
    public function testValidPassword($sPassword, $iMinLength, $iMaxLength)
    {
        $this->assertTrue($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider invalidPasswordsProvider
     */
    public function testInvalidPassword($sPassword, $iMinLength, $iMaxLength)
    {
        $this->assertFalse($this->oValidate->password($sPassword, $iMinLength, $iMaxLength));
    }

    /**
     * @dataProvider validIntegersProvider
     */
    public function testValidInteger($iNumber)
    {
        $this->assertTrue($this->oValidate->int($iNumber, 0, 60000));
    }

    /**
     * @dataProvider invalidIntegersProvider
     */
    public function testInvalidInteger($iNumber)
    {
        $this->assertFalse($this->oValidate->int($iNumber, 0, 40000));
    }

    /**
     * @dataProvider validFloatsProvider
     */
    public function testValidFloat($fFloat)
    {
        $this->assertTrue($this->oValidate->float($fFloat));
    }

    /**
     * @dataProvider invalidFloatsProvider
     */
    public function testInvalidFloat($fFloat)
    {
        $this->assertFalse($this->oValidate->float($fFloat));
    }

    /**
     * @dataProvider validDateOfBirthsProvider
     */
    public function testBirthDate($sDate, $iMinAge, $iMaxAge)
    {
        $this->assertTrue($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    /**
     * @dataProvider invalidDateOfBirthsProvider
     */
    public function testInvalidBirthDate($sDate, $iMinAge, $iMaxAge)
    {
        $this->assertFalse($this->oValidate->birthDate($sDate, $iMinAge, $iMaxAge));
    }

    /**
     * @return array
     */
    public function validHexCodesProvider()
    {
        return [
            ['#eee'],
            ['#EEE'],
            ['#eeeeee']
        ];
    }

    /**
     * @return array
     */
    public function invalidHexCodesProvider()
    {
        return [
            ['eee'],
            ['#fffffff'],
            ['#cc']
        ];
    }

    /**
     * @return array
     */
    public function validNamesProvider()
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

    /**
     * @return array
     */
    public function invalidNamesProvider()
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

    /**
     * @return array
     */
    public function validPhoneNumbersProvider()
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

    /**
     * @return array
     */
    public function invalidPhoneNumbersProvider()
    {
        return [
            ['0768374890'],
            ['0485'],
            ['zerozerozero'],
            ['']
        ];
    }

    /**
     * @return array
     */
    public function validPasswordsProvider()
    {
        return [
            ['8374878*&@*#*5r8hjvfj^', 2, 40],
            ['12345678', 4, 8]
        ];
    }

    /**
     * @return array
     */
    public function invalidPasswordsProvider()
    {
        return [
            ['1234567', 10, 30],
            ['itititkfjgk9*(&$*#&*(8342', 5, 10],
            ['', 6, 40]
        ];
    }

    /**
     * @return array
     */
    public function validIntegersProvider()
    {
        return [
            [1],
            [59868],
            [0],
            ['34']
        ];
    }

    /**
     * @return array
     */
    public function invalidIntegersProvider()
    {
        return [
            ['one'],
            ['lalal'],
            [''],
            [50000] // Exceed the maximum value set (max_range)
        ];
    }

    /**
     * @return array
     */
    public function validFloatsProvider()
    {
        return [
            [1.5],
            [0.54532],
            [0.0],
            ['3.0']
        ];
    }

    /**
     * @return array
     */
    public function invalidFloatsProvider()
    {
        return [
            ['one'],
            ['lalal'],
            ['']
        ];
    }

    /**
     * @return array
     */
    public function validDateOfBirthsProvider()
    {
        return [
            ['02/02/1989', 18, 99],
            ['02/22/1990', 20, 90],
            ['12/10/1998', 18, 80],
            ['12/10/1998', 18, 99]
        ];
    }

    /**
     * @return array
     */
    public function invalidDateOfBirthsProvider()
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
