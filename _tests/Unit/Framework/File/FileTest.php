<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / File
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\File;

use PH7\Framework\File\File;
use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    // getFileExtWithDot

    public function testGetFileExtWithDotReturnsLowercaseDotExtension(): void
    {
        $this->assertSame('.php', File::getFileExtWithDot('index.php'));
    }

    public function testGetFileExtWithDotReturnsLowercasedExtension(): void
    {
        $this->assertSame('.jpg', File::getFileExtWithDot('Photo.JPG'));
    }

    public function testGetFileExtWithDotReturnsEmptyStringWhenNoExtension(): void
    {
        $this->assertSame('', File::getFileExtWithDot('Makefile'));
    }

    public function testGetFileExtWithDotUsesLastExtensionForDoubleExtension(): void
    {
        $this->assertSame('.gz', File::getFileExtWithDot('archive.tar.gz'));
    }

    public function testGetFileExtWithDotHandlesTplExtension(): void
    {
        $this->assertSame('.tpl', File::getFileExtWithDot('layout.tpl'));
    }

    // getFileBasename

    public function testGetFileBasenameRemovesUnixTraversal(): void
    {
        $this->assertSame('config.php', File::getFileBasename('../../config.php'));
    }

    public function testGetFileBasenameRemovesWindowsTraversal(): void
    {
        $this->assertSame('config.php', File::getFileBasename('..\\..\\config.php'));
    }

    public function testGetFileBasenameRejectsDirectorySegments(): void
    {
        $this->assertSame('', File::getFileBasename('.'));
        $this->assertSame('', File::getFileBasename('..'));
    }

    public function testGetFileBasenameRejectsNullBytes(): void
    {
        $this->assertSame('', File::getFileBasename("photo.jpg\0.php"));
    }

    // isPathInsideDirectory

    public function testIsPathInsideDirectoryReturnsTrueForDirectChild(): void
    {
        $this->assertTrue(File::isPathInsideDirectory('/var/www/public/style.css', '/var/www/public'));
    }

    public function testIsPathInsideDirectoryReturnsTrueForNestedPath(): void
    {
        $this->assertTrue(File::isPathInsideDirectory('/var/www/public/themes/base/tpl/layout.tpl', '/var/www/public'));
    }

    public function testIsPathInsideDirectoryReturnsFalseForOutsidePath(): void
    {
        $this->assertFalse(File::isPathInsideDirectory('/etc/passwd', '/var/www/public'));
    }

    public function testIsPathInsideDirectoryReturnsFalseForTraversalAttempt(): void
    {
        // Path that starts with base dir string but is outside it (path traversal variant)
        $this->assertFalse(File::isPathInsideDirectory('/var/www/public-evil/secret.php', '/var/www/public'));
    }

    public function testIsPathInsideDirectoryHandlesTrailingSlashOnDirectory(): void
    {
        $this->assertTrue(File::isPathInsideDirectory('/var/www/public/file.js', '/var/www/public/'));
    }

    public function testIsPathInsideDirectoryReturnsFalseWhenPathEqualsDirectoryWithoutSeparator(): void
    {
        // The directory itself is not "inside" itself as a file
        $this->assertFalse(File::isPathInsideDirectory('/var/www/public', '/var/www/public-other'));
    }
}
