<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Video;

use PH7\Framework\Video\Video;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VideoUploadValidationTest extends TestCase
{
    #[DataProvider('mimeReliabilityProvider')]
    public function testOnlyServerDetectedConcreteMimeIsReliable(string $sMimeType, bool $bExpected): void
    {
        $oReflection = new \ReflectionClass(Video::class);
        $oVideo = $oReflection->newInstanceWithoutConstructor();
        $oMethod = $oReflection->getMethod('isReliableDetectedMime');

        $this->assertSame($bExpected, $oMethod->invoke($oVideo, $sMimeType));
    }

    public function testValidationNeverFallsBackToBrowserSuppliedMime(): void
    {
        $sSource = file_get_contents(PH7_PATH_FRAMEWORK . 'Video/Video.class.php');

        $this->assertIsString($sSource);
        $this->assertStringNotContainsString('$this->aFile[\'type\']', $sSource);
        $this->assertStringContainsString('$this->isReliableDetectedMime($sDetectedMime)', $sSource);
        $this->assertStringContainsString('$this->mimeMatchesExpected($sDetectedMime, $sExpectedMime)', $sSource);
    }

    public static function mimeReliabilityProvider(): array
    {
        return [
            'empty detection' => ['', false],
            'generic binary detection' => ['application/octet-stream', false],
            'mixed-case generic detection' => [' Application/Octet-Stream ', false],
            'server-detected video' => ['video/mp4', true],
            'recognized alias' => ['application/ogg', true]
        ];
    }
}
