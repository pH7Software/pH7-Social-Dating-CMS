<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Geo / Misc
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Geo\Misc;

use PH7\Framework\Geo\Misc\Country;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase
{
    public function testGbCountryCode(): void
    {
        $sCountryCode = Country::fixCode('GB');
        $sExpectedCountryCode = 'UK';
        $this->assertSame($sExpectedCountryCode, $sCountryCode);
    }
}
