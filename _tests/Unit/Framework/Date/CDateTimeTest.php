<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Date
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Date;

use PH7\Framework\Date\CDateTime;
use PHPUnit\Framework\TestCase;

final class CDateTimeTest extends TestCase
{
    private const STATIC_DATETIME = '2018-05-30 08:02:11';
    private const TIMEZONE = 'UTC';

    private CDateTime $oDateTime;

    protected function setUp(): void
    {
        $this->oDateTime = (new CDateTime)->get(self::STATIC_DATETIME, self::TIMEZONE);
    }

    public function testDateTime(): void
    {
        $sResult = $this->oDateTime->dateTime('d-m-Y H:i:s');
        $this->assertSame('30-05-2018 08:02:11', $sResult);
    }

    public function testDate(): void
    {
        $sResult = $this->oDateTime->date('m-d-Y');
        $this->assertSame('05-30-2018', $sResult);
    }

    public function testTime(): void
    {
        $sResult = $this->oDateTime->time('H i, s');
        $this->assertSame('08 02, 11', $sResult);
    }
}
