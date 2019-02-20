<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Date
 */

namespace PH7\Test\Unit\Framework\Date;

use PH7\Framework\Date\Various;
use PHPUnit_Framework_TestCase;

class VariousTest extends PHPUnit_Framework_TestCase
{
    const STATIC_DATETIME = '2018-05-30 08:02:11';

    public function testGetTime()
    {
        $iResult = Various::getTime(self::STATIC_DATETIME);

        $this->assertSame(1527667331, $iResult);
    }

    public function testTimeToSec()
    {
        $iResult = Various::timeToSec('01:02:11');

        // 1 hour, 2 minutes, 11 seconds = 3731 secs
        $this->assertSame(3731, $iResult);
    }

    /**
     * @param int $iSec
     * @param string $sTime
     *
     * @dataProvider secAndTimeProvider
     */
    public function testSecToTime($iSec, $sTime)
    {
        $sResult = Various::secToTime($iSec);

        $this->assertSame($sTime, $sResult);
    }

    /**
     * @return array
     */
    public function secAndTimeProvider()
    {
        return [
            [563, '09:23'],
            [60, '01:00'],
            [121, '02:01'],
            [4802, '80:02']
        ];
    }
}
