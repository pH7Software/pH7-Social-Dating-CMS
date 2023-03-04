<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Xml / Inc / Classes
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Xml\Inc\Classes;

require_once PH7_PATH_SYS_MOD . 'xml/inc/class/DateFormat.php';

use PH7\DateFormat;
use PHPUnit\Framework\TestCase;

final class DateFormatTest extends TestCase
{
    public function testRssFormat(): void
    {
        $sExpected = 'Mon, 20 Nov 2017 00:00:00 +0000';
        $oDateFormat = DateFormat::getRss('20 November 2017');

        $this->assertSame($sExpected, $oDateFormat);
    }

    public function testSitemapFormat(): void
    {
        $sExpected = '2017-11-20T00:00:00+00:00';
        $oDateFormat = DateFormat::getSitemap('20 November 2017');

        $this->assertSame($sExpected, $oDateFormat);
    }
}
