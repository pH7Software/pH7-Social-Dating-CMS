<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Image
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Image;

use PH7\Framework\Image\FileStorage;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class FileStorageTest extends TestCase
{
    public function testDestroyingStorageDoesNotDeleteCallerOwnedSource(): void
    {
        $sSource = tempnam(sys_get_temp_dir(), 'ph7-image-source-');

        $this->assertNotFalse($sSource);

        try {
            $oStorage = new FileStorage($sSource);
            unset($oStorage);

            $this->assertFileExists($sSource);
        } finally {
            @unlink($sSource);
        }
    }

    public function testSourcePixelLimitRejectsDecompressionBombDimensions(): void
    {
        $oMethod = new ReflectionMethod(FileStorage::class, 'areSourceDimensionsSafe');

        $this->assertTrue($oMethod->invoke(null, 4032, 3024));
        $this->assertFalse($oMethod->invoke(null, 5000, 2501));
        $this->assertFalse($oMethod->invoke(null, 0, 1));
    }

    public function testMalformedImageWithValidSignatureIsRejected(): void
    {
        $sSource = tempnam(sys_get_temp_dir(), 'ph7-invalid-gif-');

        $this->assertNotFalse($sSource);
        file_put_contents(
            $sSource,
            'GIF89a' . pack('v', 1) . pack('v', 1) . "\x80\0\0" . str_repeat("\0", 6) . 'invalid'
        );

        try {
            $this->assertSame(IMAGETYPE_GIF, exif_imagetype($sSource));
            $this->assertFalse((new FileStorage($sSource))->validate());
        } finally {
            @unlink($sSource);
        }
    }

    public function testGifCanBeLoadedAndSavedOnPhp8(): void
    {
        $sSource = tempnam(sys_get_temp_dir(), 'ph7-gif-source-');
        $sDestination = tempnam(sys_get_temp_dir(), 'ph7-gif-destination-');
        $rImage = imagecreatetruecolor(2, 2);

        $this->assertNotFalse($sSource);
        $this->assertNotFalse($sDestination);
        $this->assertNotFalse($rImage);
        $this->assertTrue(imagegif($rImage, $sSource));

        try {
            $oStorage = new FileStorage($sSource);
            $this->assertTrue($oStorage->validate());
            $this->assertSame($oStorage, $oStorage->save($sDestination));
            $this->assertSame(IMAGETYPE_GIF, exif_imagetype($sDestination));
        } finally {
            unset($rImage);
            @unlink($sSource);
            @unlink($sDestination);
        }
    }
}
