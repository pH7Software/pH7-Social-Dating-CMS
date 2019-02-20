<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Date
 */

namespace PH7\Test\Unit\Framework\Date;

use PH7\Framework\Date\CDateTime;
use PHPUnit_Framework_TestCase;

class CDateTimeTest extends PHPUnit_Framework_TestCase
{
    const STATIC_DATETIME = '2018-05-30 08:02:11';
    const TIMEZONE = 'UTC';

    /** @var CDateTime */
    private $oDateTime;

    protected function setUp()
    {
        $this->oDateTime = (new CDateTime)->get(self::STATIC_DATETIME, self::TIMEZONE);
    }

    public function testDateTime()
    {
        $sResult = $this->oDateTime->dateTime('d-m-Y H:i:s');
        $this->assertSame('30-05-2018 08:02:11', $sResult);
    }

    public function testDate()
    {
        $sResult = $this->oDateTime->date('m-d-Y');
        $this->assertSame('05-30-2018', $sResult);
    }

    public function testTime()
    {
        $sResult = $this->oDateTime->time('H i, s');
        $this->assertSame('08 02, 11', $sResult);
    }
}
