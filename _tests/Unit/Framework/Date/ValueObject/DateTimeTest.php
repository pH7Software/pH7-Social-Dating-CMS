<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Date / ValueObject
 */

namespace PH7\Test\Unit\Framework\Date\ValueObject;

use PH7\Framework\Date\ValueObject\DateTime as VODateTime;
use PH7\Framework\Date\ValueObject\InvalidDateFormatException;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testGetDateTimeValue(): void
    {
        $sDateValue = '2018-05-31 10:00:05';

        $oDatetime = new VODateTime($sDateValue);
        $this->assertSame($sDateValue, $oDatetime->asString());
    }

    public function testInvalidDateTime(): void
    {
        $this->expectException(InvalidDateFormatException::class);

        new VODateTime('31-05-2018 10:00:05');
    }
}
