<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Security
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security;

use DOMDocument;
use PH7\Framework\Security\Version;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class VersionTest extends TestCase
{
    #[DataProvider('invalidReleaseXmlProvider')]
    public function testMalformedReleaseMetadataFailsClosed(string $sXml): void
    {
        $this->assertFalse($this->parseReleaseXml($sXml));
    }

    public function testCompleteReleaseMetadataIsNormalized(): void
    {
        $this->assertSame(
            [
                'is_alert' => true,
                'name' => 'REVOLUTIONARY™',
                'version' => '18.6.1',
                'build' => '1'
            ],
            $this->parseReleaseXml(
                '<software><ph7><ph7builder><upd-alert>true</upd-alert><name> REVOLUTIONARY™ </name>' .
                '<version>18.6.1</version><build>1</build></ph7builder></ph7></software>'
            )
        );
    }

    public static function invalidReleaseXmlProvider(): iterable
    {
        yield 'missing package' => ['<software><ph7 /></software>'];
        yield 'missing version' => [
            '<software><ph7><ph7builder><name>Release</name><build>1</build></ph7builder></ph7></software>'
        ];
        yield 'invalid version' => [
            '<software><ph7><ph7builder><name>Release</name><version>latest</version>' .
            '<build>1</build></ph7builder></ph7></software>'
        ];
        yield 'invalid build' => [
            '<software><ph7><ph7builder><name>Release</name><version>18.6.1</version>' .
            '<build>beta</build></ph7builder></ph7></software>'
        ];
    }

    private function parseReleaseXml(string $sXml): array|bool
    {
        $oDom = new DOMDocument;
        $this->assertTrue($oDom->loadXML($sXml));

        $oMethod = new ReflectionMethod(Version::class, 'parseLatestInfo');

        return $oMethod->invoke(null, $oDom);
    }
}
