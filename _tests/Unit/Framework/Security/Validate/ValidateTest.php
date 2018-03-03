<?php
/**
 * @author           Pierre-Henry Soria <hi@ph7.me>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
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
    public function testValidName($sName)
    {
        $this->assertTrue($this->oValidate->name($sName));
    }

    /**
     * @dataProvider invalidNamesProvider
     */
    public function testInvalidName($sName)
    {
        $this->assertFalse($this->oValidate->name($sName));
    }

    /**
     * @dataProvider validIntegersProvider
     */
    public function testValidInteger($iNumbers)
    {
        $this->assertTrue($this->oValidate->int($iNumbers, 0, 60000));
    }

    /**
     * @dataProvider invalidIntegersProvider
     */
    public function testInvalidInteger($iNumbers)
    {
        $this->assertFalse($this->oValidate->int($iNumbers, 0, 40000));
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
            ['Píėrre'],
            ['Amélie'],
            ['Pierre-Henry'],
            ['Pierre-Henry Soria'],
            ['Àngel Nøisã'],
            ['Nôël Großkreutz']
        ];
    }

    /**
     * @return array
     */
    public function invalidNamesProvider()
    {
        return [
            ['o'],
            ['{NOT A NAME}'],
            ['*&^'],
            ['http://affiliate-site.com'],
            ['https://spam'],
            [4335],
            ['$money$'],
            ['James€']
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
}
