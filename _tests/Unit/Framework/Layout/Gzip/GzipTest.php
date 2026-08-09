<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Layout / Gzip
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Layout\Gzip;

use PH7\Framework\Layout\Gzip\Gzip;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class GzipTest extends TestCase
{
    public function testNestedAssetPathRemainsValid(): void
    {
        $this->assertSame('jquery/slick.js', $this->normalizeRelativePath('jquery/slick.js'));
    }

    public function testRootDirectoryMarkerIsHandledByTheRequestBoundary(): void
    {
        $oMethod = new ReflectionMethod(Gzip::class, 'normalizeDirectoryPath');
        $oGzip = (new \ReflectionClass(Gzip::class))->newInstanceWithoutConstructor();

        $this->assertSame('', $oMethod->invoke($oGzip, '.'));
        $this->assertSame('static/css', $oMethod->invoke($oGzip, 'static/css/'));
        $this->assertNull($oMethod->invoke($oGzip, '../_protected'));
    }

    #[DataProvider('unsafePathProvider')]
    public function testUnsafeAssetPathIsRejected(string $sPath): void
    {
        $this->assertSame('', $this->normalizeRelativePath($sPath));
    }

    public static function unsafePathProvider(): array
    {
        return [
            'empty path' => [''],
            'parent traversal' => ['../secret.css'],
            'nested traversal' => ['theme/../../secret.css'],
            'current directory' => ['./style.css'],
            'empty segment' => ['theme//style.css'],
            'null byte' => ["style.css\0.php"]
        ];
    }

    private function normalizeRelativePath(string $sPath): string
    {
        $oMethod = new ReflectionMethod(Gzip::class, 'normalizeRelativePath');

        return $oMethod->invoke((new \ReflectionClass(Gzip::class))->newInstanceWithoutConstructor(), $sPath);
    }
}
