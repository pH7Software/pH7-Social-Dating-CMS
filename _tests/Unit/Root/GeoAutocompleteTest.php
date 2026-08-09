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
    private const PROJECT_ROOT = __DIR__ . '/../../..';

    public function testCityAutocompleteUsesTheSecureGeoNamesEndpoint(): void
    {
        $sScript = file_get_contents(self::PROJECT_ROOT . '/static/js/geo/autocompleteCity.js');
        $sDocumentation = file_get_contents(self::PROJECT_ROOT . '/static/js/geo/geo_api.txt');

        $this->assertIsString($sScript);
        $this->assertIsString($sDocumentation);
        $this->assertStringContainsString('https://secure.geonames.org/searchJSON?', $sScript);
        $this->assertStringContainsString("autocompleteCityInit('ph7cms')", $sScript);
        $this->assertStringNotContainsString('http://ws.geonames.org', $sScript);
        $this->assertStringNotContainsString('http://www.geonames.org', $sScript);
        $this->assertStringContainsString('https://www.geonames.org/', $sDocumentation);
    }
}
