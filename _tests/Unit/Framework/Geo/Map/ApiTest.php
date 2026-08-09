<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Geo / Map
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Geo\Map;

use ErrorException;
use PH7\Framework\Geo\Map\Api as ApiMap;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class ApiTest extends TestCase
{
    public function testApiKeyIsSet(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey('OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU');
        $this->assertFalse($oMap->isApiKeyNotSet());
    }

    public function testWrongApiKeySet(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey('invalid');
        $this->assertTrue($oMap->isApiKeyNotSet());
    }

    public function testApiKeyIsNotSet(): void
    {
        $oMap = new ApiMap;
        $this->assertTrue($oMap->isApiKeyNotSet());
    }

    public function testKmlImportDoesNotEmitPhpDeprecation(): void
    {
        $sFixturePath = tempnam(sys_get_temp_dir(), 'ph7-kml-');
        $this->assertIsString($sFixturePath);
        file_put_contents(
            $sFixturePath,
            '<kml><Document><Folder><Placemark><name>Test place</name><Point><coordinates>153.02,-27.47</coordinates></Point></Placemark></Folder></Document></kml>'
        );

        set_error_handler(
            static function (int $iSeverity, string $sMessage, string $sFile, int $iLine): bool {
                if ($iSeverity === E_DEPRECATED) {
                    throw new ErrorException($sMessage, 0, $iSeverity, $sFile, $iLine);
                }

                return false;
            }
        );

        try {
            $oMap = new ApiMap;
            $oMap->addKML($sFixturePath);
        } finally {
            restore_error_handler();
            unlink($sFixturePath);
        }

        $oContentMarker = new ReflectionProperty(ApiMap::class, 'contentMarker');
        $this->assertStringContainsString('Test place', $oContentMarker->getValue($oMap));
    }
}
