<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / CArray
 */

namespace PH7\Test\Unit\Framework\CArray;

use PH7\Framework\CArray\ObjArr;
use PHPUnit_Framework_TestCase;
use stdClass;

class ObjArrTest extends PHPUnit_Framework_TestCase
{
    public function testToObject()
    {
        $aData = ['one' => 'abc', 'two' => 'def', 'three' => 'ghi'];

        $oResults = ObjArr::toObject($aData);

        $oExpected = new stdClass();
        $oExpected->one = 'abc';
        $oExpected->two = 'def';
        $oExpected->three = 'ghi';

        $this->assertInstanceOf(stdClass::class, $oResults);
        $this->assertEquals($oExpected, $oResults);
    }

    public function testToArray()
    {
        $oData = new stdClass();
        $oData->one = 'abc';
        $oData->two = 'def';
        $oData->three = 'ghi';

        $aResults = ObjArr::toArray($oData);

        $aExpected = ['one' => 'abc', 'two' => 'def', 'three' => 'ghi'];

        $this->assertSame($aExpected, $aResults);
    }
}
