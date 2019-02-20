<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / CArray
 */

namespace PH7\Test\Unit\Framework\CArray;

use PH7\Framework\CArray\CArray;
use PHPUnit_Framework_TestCase;

class CArrayTest extends PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $aArrayOne = ['abc', 'haha', 'Pierre'];
        $aArrayTwo = ['Julie', 'Amelie', 'Pied', 'manger'];

        $aResults = CArray::merge($aArrayOne, $aArrayTwo);

        $this->assertSame(
            [
                0 => 'abc',
                1 => 'haha',
                2 => 'Pierre',
                3 => 'Julie',
                4 => 'Amelie',
                5 => 'Pied',
                6 => 'manger'
            ],
            $aResults
        );
    }

    public function testKeyByValueExists()
    {
        $aData = ['James', 'henry' => 'Henry', 'pierre' => 'Pierre'];

        $sResult = CArray::getKeyByValue('Pierre', $aData);

        $this->assertSame('pierre', $sResult);
    }

    public function testKeyByValueDoesntExist()
    {
        $aData = ['James', 'henry' => 'Henry'];

        $sResult = CArray::getKeyByValue('Pierre', $aData);

        $this->assertNull($sResult);
    }

    public function testKeyByValueIgnoreCaseExists()
    {
        $aData = ['pierre' => 'Pierre', 'henry' => 'Henry'];

        $sResult = CArray::getKeyByValueIgnoreCase('PIErrE', $aData);

        $this->assertSame('pierre', $sResult);
    }

    public function testKeyByValueIgnoreCaseDoesntExist()
    {
        $aData = ['henry' => 'Henry'];

        $sResult = CArray::getKeyByValueIgnoreCase('PIErrE', $aData);

        $this->assertNull($sResult);
    }

    public function testValueByKeyExists()
    {
        $aData = ['key' => 'JAMES'];

        $sResult = CArray::getValueByKey('key', $aData);

        $this->assertSame('JAMES', $sResult);
    }

    public function testValueByKeyDoesntExist()
    {
        $aData = ['key' => 'JAMES'];

        $sResult = CArray::getValueByKey('invalid', $aData);

        $this->assertNull($sResult);
    }
}
