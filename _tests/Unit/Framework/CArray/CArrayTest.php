<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / CArray
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\CArray;

use PH7\Framework\CArray\CArray;
use PHPUnit\Framework\TestCase;

final class CArrayTest extends TestCase
{
    public function testMerge(): void
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

    public function testKeyByValueExists(): void
    {
        $aData = ['James', 'henry' => 'Henry', 'pierre' => 'Pierre'];

        $sResult = CArray::getKeyByValue('Pierre', $aData);

        $this->assertSame('pierre', $sResult);
    }

    public function testKeyByValueDoesntExist(): void
    {
        $aData = ['James', 'henry' => 'Henry'];

        $sResult = CArray::getKeyByValue('Pierre', $aData);

        $this->assertNull($sResult);
    }

    public function testKeyByValueIgnoreCaseExists(): void
    {
        $aData = ['pierre' => 'Pierre', 'henry' => 'Henry'];

        $sResult = CArray::getKeyByValueIgnoreCase('PIErrE', $aData);

        $this->assertSame('pierre', $sResult);
    }

    public function testKeyByValueIgnoreCaseDoesntExist(): void
    {
        $aData = ['henry' => 'Henry'];

        $sResult = CArray::getKeyByValueIgnoreCase('PIErrE', $aData);

        $this->assertNull($sResult);
    }

    public function testValueByKeyExists(): void
    {
        $aData = ['key' => 'JAMES'];

        $sResult = CArray::getValueByKey('key', $aData);

        $this->assertSame('JAMES', $sResult);
    }

    public function testValueByKeyDoesntExist(): void
    {
        $aData = ['key' => 'JAMES'];

        $sResult = CArray::getValueByKey('invalid', $aData);

        $this->assertNull($sResult);
    }
}
