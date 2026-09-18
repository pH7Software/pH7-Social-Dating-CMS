<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Root;

use PHPUnit\Framework\TestCase;

final class GeoAutocompleteTest extends TestCase
{
    public function testCityAutocompleteUsesTheSecureGeoNamesEndpoint(): void
    {
        $sScript = file_get_contents(self::projectRoot() . '/static/js/geo/autocompleteCity.js');
        $sDocumentation = file_get_contents(self::projectRoot() . '/static/js/geo/geo_api.txt');

        $this->assertIsString($sScript);
        $this->assertIsString($sDocumentation);
        $this->assertStringContainsString("url: 'https://secure.geonames.org/searchJSON',", $sScript);
        $this->assertStringContainsString('username: sGeonamesUsername', $sScript);
        $this->assertStringContainsString('country: sCountry', $sScript);
        $this->assertStringContainsString("autocompleteCityInit('ph7cms')", $sScript);
        $this->assertStringNotContainsString('http://ws.geonames.org', $sScript);
        $this->assertStringNotContainsString('http://www.geonames.org', $sScript);
        $this->assertStringContainsString('https://www.geonames.org/', $sDocumentation);
    }

    private static function projectRoot(): string
    {
        return dirname(PH7_PATH_PROTECTED);
    }
}
