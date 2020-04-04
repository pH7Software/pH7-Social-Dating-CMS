<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Geo / Misc
 */

namespace PH7\Test\Unit\Framework\Geo\Misc;

use PH7\Framework\Geo\Misc\Country;
use PHPUnit_Framework_TestCase;

class CountryTest extends PHPUnit_Framework_TestCase
{
    public function testGbCountryCode()
    {
        $sCountryCode = Country::fixCode('GB');
        $sExpectedCountryCode = 'UK';
        $this->assertSame($sExpectedCountryCode, $sCountryCode);
    }
}
