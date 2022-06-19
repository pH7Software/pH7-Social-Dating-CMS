<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Date
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Date;

use PH7\Framework\Date\Various;
use PHPUnit\Framework\TestCase;

class VariousTest extends TestCase
{
    private const STATIC_DATETIME = '2018-05-30 08:02:11';

    public function testGetTime(): void
    {
        $iResult = Various::getTime(self::STATIC_DATETIME);

        $this->assertSame(1527667331, $iResult);
    }

    public function testTimeToSec(): void
    {
        $iResult = Various::timeToSec('01:02:11');

        // 1 hour, 2 minutes, 11 seconds = 3731 secs
        $this->assertSame(3731, $iResult);
    }

    /**
     * @dataProvider secAndTimeProvider
     */
    public function testSecToTime(int $iSec, string $sTime): void
    {
        $sResult = Various::secToTime($iSec);

        $this->assertSame($sTime, $sResult);
    }

    public function secAndTimeProvider(): array
    {
        return [
            [563, '09:23'],
            [60, '01:00'],
            [121, '02:01'],
            [4802, '80:02']
        ];
    }
}
