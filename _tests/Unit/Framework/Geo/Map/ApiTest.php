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
    private const API_KEY = 'OIzaSyBu-916IsoKajomJNIgngS6HL_kDIKU0aU';
    private const JAVASCRIPT_JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP |
        JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;

    public function testApiKeyIsSet(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey(self::API_KEY);
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

    public function testMapWithoutApiKeyDoesNotRequestGoogleMaps(): void
    {
        $oMap = new ApiMap;
        $oMap->generate();

        $oContent = new ReflectionProperty(ApiMap::class, 'content');
        $sContent = $oContent->getValue($oMap);
        $this->assertIsString($sContent);
        $this->assertStringNotContainsString(ApiMap::GOOGLE_API_URL, $sContent);
        $this->assertStringNotContainsString('google.maps.', $sContent);
        $this->assertStringContainsString('Google Maps API key', $sContent);
    }

    public function testGeocodingWithoutApiKeyDoesNotMakeRequest(): void
    {
        $oMap = new class extends ApiMap {
            public bool $bRequestMade = false;

            public function getContent($url)
            {
                $this->bRequestMade = true;

                return '';
            }
        };

        $this->assertNull($oMap->geocoding('Wellington'));
        $this->assertFalse($oMap->bRequestMade);
    }

    public function testFailedGeocodingDoesNotExposeApiKey(): void
    {
        $oMap = new class extends ApiMap {
            public function getContent($url)
            {
                return '';
            }
        };
        $oMap->setKey(self::API_KEY);

        ob_start();
        $oMap->geocoding('Wellington');
        $sOutput = (string)ob_get_clean();

        $this->assertStringContainsString('Google Maps API request failed', $sOutput);
        $this->assertStringNotContainsString(self::API_KEY, $sOutput);
    }

    public function testCenterAddressIsEncodedForJavaScript(): void
    {
        $sCenter = 'Wellington");alert(document.domain);//</script>';
        $sEncodedCenter = json_encode(
            $sCenter,
            self::JAVASCRIPT_JSON_FLAGS
        );

        foreach ([false, true] as $bEnableGeolocation) {
            $oMap = new ApiMap;
            $oMap->setKey(self::API_KEY);
            $oMap->setCenter($sCenter);
            $oMap->setEnableGeolocation($bEnableGeolocation);
            $oMap->generate();

            $oContent = new ReflectionProperty(ApiMap::class, 'content');
            $sContent = $oContent->getValue($oMap);
            $this->assertIsString($sContent);
            $this->assertStringContainsString('geocodeCenter(' . $sEncodedCenter . ');', $sContent);
            $this->assertStringNotContainsString('geocodeCenter("' . $sCenter . '");', $sContent);
        }
    }

    public function testMarkerArgumentsAreEncodedForJavaScript(): void
    {
        $sPayload = '");alert(document.domain);//</script>';
        $sHtmlPayload = '<strong>Safe</strong><img src=x onerror=alert(document.domain)>';
        $sEncodedPayload = json_encode($sPayload, self::JAVASCRIPT_JSON_FLAGS);
        $oMap = new ApiMap;
        $oMap->addMarkerByCoords('-27.47', '153.02', $sPayload, $sHtmlPayload, $sPayload, $sPayload, $sPayload);

        $oContentMarker = new ReflectionProperty(ApiMap::class, 'contentMarker');
        $sContentMarker = $oContentMarker->getValue($oMap);
        $this->assertIsString($sContentMarker);
        $this->assertSame(4, substr_count($sContentMarker, $sEncodedPayload));
        $this->assertStringContainsString('Safe', $sContentMarker);
        $this->assertStringNotContainsString('onerror', $sContentMarker);
        $this->assertStringNotContainsString($sPayload, $sContentMarker);
    }

    public function testDirectionAddressesAreEncodedForJavaScript(): void
    {
        $sPayload = '");alert(document.domain);//</script>';
        $sEncodedPayload = json_encode($sPayload, self::JAVASCRIPT_JSON_FLAGS);
        $oMap = new ApiMap;
        $oMap->addDirection($sPayload, $sPayload);

        $oContentMarker = new ReflectionProperty(ApiMap::class, 'contentMarker');
        $this->assertSame(
            'addDirection(' . $sEncodedPayload . ',' . $sEncodedPayload . ');',
            $oContentMarker->getValue($oMap)
        );
    }

    public function testCenterCoordinatesAreSerializedAsNumbers(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey(self::API_KEY);
        $oMap->setCenterLatLng('-27.47', '153.02');
        $oMap->generate();

        $oContent = new ReflectionProperty(ApiMap::class, 'content');
        $this->assertStringContainsString(
            'setCenter(new google.maps.LatLng(-27.47, 153.02));',
            $oContent->getValue($oMap)
        );
    }

    public function testInvalidCoordinateExpressionIsRejected(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $oMap = new ApiMap;
        $oMap->setCenterLatLng('0);alert(document.domain);//', '153.02');
    }

    public function testMapElementIdentifiersAreEncodedForTheirOutputContexts(): void
    {
        $sMapId = 'map-id"><script>alert(document.domain)</script>';
        $sDirectionId = 'route-id");alert(document.domain);//';
        $oMap = new ApiMap;
        $oMap->setKey(self::API_KEY);
        $oMap->setDivId($sMapId);
        $oMap->setDirectionDivId($sDirectionId);
        $oMap->generate();

        $oContent = new ReflectionProperty(ApiMap::class, 'content');
        $sContent = $oContent->getValue($oMap);
        $this->assertIsString($sContent);
        $this->assertStringContainsString('id="' . htmlspecialchars($sMapId, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"', $sContent);
        $this->assertStringContainsString(
            'document.getElementById(' . json_encode($sMapId, self::JAVASCRIPT_JSON_FLAGS) . ')',
            $sContent
        );
        $this->assertStringContainsString(
            'document.getElementById(' . json_encode($sDirectionId, self::JAVASCRIPT_JSON_FLAGS) . ')',
            $sContent
        );
        $this->assertStringNotContainsString('<script>alert(document.domain)</script>', $sContent);
    }

    public function testMapUsesModernLoadListenerAndAvoidsDocumentWrite(): void
    {
        $oMap = new ApiMap;
        $oMap->setKey(self::API_KEY);
        $oMap->generate();

        $oContent = new ReflectionProperty(ApiMap::class, 'content');
        $sContent = $oContent->getValue($oMap);
        $this->assertIsString($sContent);
        $this->assertStringContainsString(
            'window.addEventListener("load", func, {once:true})',
            $sContent
        );
        $this->assertStringNotContainsString('document.write(', $sContent);
    }

    public function testNumericMapOptionsCannotBecomeJavaScriptExpressions(): void
    {
        $sPayload = ');alert(document.domain);//';
        $oMap = new ApiMap;
        $oMap->setKey(self::API_KEY);
        $oMap->setZoom('9' . $sPayload);
        $oMap->setInfoWindowWidth('250' . $sPayload);
        $oMap->setInfoWindowZoom('6' . $sPayload);
        $oMap->setIconSize('20' . $sPayload, '34' . $sPayload);
        $oMap->setIconAnchorSize('1' . $sPayload, '2' . $sPayload);
        $oMap->setEnableWindowZoom(true);
        $oMap->setClusterer(true, '100' . $sPayload, '9' . $sPayload);
        $oMap->generate();

        $oContent = new ReflectionProperty(ApiMap::class, 'content');
        $sContent = $oContent->getValue($oMap);
        $this->assertIsString($sContent);
        $this->assertStringContainsString('zoom: 9,', $sContent);
        $this->assertStringContainsString('new google.maps.Size(20, 34)', $sContent);
        $this->assertStringContainsString('new google.maps.Point(1,2)', $sContent);
        $this->assertStringNotContainsString('alert(document.domain)', $sContent);
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
