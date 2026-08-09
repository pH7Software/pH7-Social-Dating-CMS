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
use ZipArchive;

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

    public function testSaveWritesAGroupWritableFileWithoutWritingItsDirectory(): void
    {
        $sDirectory = sys_get_temp_dir() . '/ph7-file-save-' . bin2hex(random_bytes(8));
        $sTarget = $sDirectory . '/settings.ini';
        mkdir($sDirectory, 0700);
        file_put_contents($sTarget, 'old');
        chmod($sTarget, 0660);
        chmod($sDirectory, 0550);

        try {
            $this->assertSame(9, (new File)->save($sTarget, 'new-value'));
            $this->assertSame('new-value', file_get_contents($sTarget));
            $this->assertSame(0660, fileperms($sTarget) & 0777);
            $this->assertFileDoesNotExist($sDirectory . '/settings.tmp.ini');
        } finally {
            chmod($sDirectory, 0700);
            $this->removeTestTree($sDirectory);
        }
    }

    public function testSaveReturnsZeroAfterWritingEmptyContents(): void
    {
        $sTarget = tempnam(sys_get_temp_dir(), 'ph7-file-target-');

        try {
            $this->assertNotFalse($sTarget);
            $this->assertSame(0, (new File)->save($sTarget, ''));
            $this->assertSame('', file_get_contents($sTarget));
        } finally {
            @unlink($sTarget);
        }
    }

    public function testSaveReturnsFalseWhenTheDestinationCannotBeCopied(): void
    {
        $sDirectory = sys_get_temp_dir() . '/ph7-file-save-' . bin2hex(random_bytes(8));
        mkdir($sDirectory, 0700);

        try {
            $this->assertFalse((new File)->save($sDirectory . '/missing/file.txt', 'value'));
        } finally {
            $this->removeTestTree($sDirectory);
        }
    }

    public function testZipExtractAcceptsContainedFiles(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['folder/readme.txt' => 'safe']);

        try {
            $this->assertTrue((new File)->zipExtract($sArchive, $sDestination));
            $this->assertSame('safe', file_get_contents($sDestination . '/folder/readme.txt'));
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
        }
    }

    public function testZipExtractRejectsTraversal(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['../outside.txt' => 'unsafe']);
        $sOutsidePath = dirname($sDestination) . '/outside.txt';

        try {
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
            $this->assertFileDoesNotExist($sOutsidePath);
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
            @unlink($sOutsidePath);
        }
    }

    public function testZipExtractRejectsAbsolutePath(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['/absolute.txt' => 'unsafe']);

        try {
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
        }
    }

    public function testZipExtractRejectsWindowsDrivePath(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['C:\\Windows\\system.ini' => 'unsafe']);

        try {
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
        }
    }

    public function testZipExtractRejectsBackslashTraversal(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['..\\outside.txt' => 'unsafe']);
        $sOutsidePath = dirname($sDestination) . '/outside.txt';

        try {
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
            $this->assertFileDoesNotExist($sOutsidePath);
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
            @unlink($sOutsidePath);
        }
    }

    public function testZipExtractRejectsArchiveSymlink(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['link' => '../outside.txt'], ['link']);

        try {
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
        }
    }

    public function testZipExtractRejectsInvalidArchive(): void
    {
        $sArchive = tempnam(sys_get_temp_dir(), 'ph7-invalid-zip-');
        $sDestination = sys_get_temp_dir() . '/ph7-unzip-' . bin2hex(random_bytes(8));

        try {
            $this->assertNotFalse($sArchive);
            $this->assertSame(9, file_put_contents($sArchive, 'not a zip'));
            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
        } finally {
            $this->removeTestTree($sDestination);
            @unlink($sArchive);
        }
    }

    public function testZipExtractRejectsExistingSymlinkParent(): void
    {
        [$sArchive, $sDestination] = $this->createArchive(['folder/readme.txt' => 'unsafe']);
        $sOutside = sys_get_temp_dir() . '/ph7-unzip-outside-' . bin2hex(random_bytes(8));
        mkdir($sDestination, 0755, true);
        mkdir($sOutside, 0755, true);

        try {
            if (!@symlink($sOutside, $sDestination . '/folder')) {
                $this->markTestSkipped('Symbolic links are unavailable in this environment.');
            }

            $this->assertFalse((new File)->zipExtract($sArchive, $sDestination));
            $this->assertFileDoesNotExist($sOutside . '/readme.txt');
        } finally {
            @unlink($sDestination . '/folder');
            $this->removeTestTree($sDestination);
            $this->removeTestTree($sOutside);
            @unlink($sArchive);
        }
    }

    private function createArchive(array $aFiles, array $aSymlinks = []): array
    {
        $sArchive = tempnam(sys_get_temp_dir(), 'ph7-zip-');
        $sDestination = sys_get_temp_dir() . '/ph7-unzip-' . bin2hex(random_bytes(8));
        $oZip = new ZipArchive;

        $this->assertNotFalse($sArchive);
        $this->assertTrue($oZip->open($sArchive, ZipArchive::OVERWRITE));
        foreach ($aFiles as $sName => $sContents) {
            $this->assertTrue($oZip->addFromString($sName, $sContents));
        }
        foreach ($aSymlinks as $sName) {
            $this->assertTrue(
                $oZip->setExternalAttributesName($sName, ZipArchive::OPSYS_UNIX, 0120777 << 16)
            );
        }
        $this->assertTrue($oZip->close());

        return [$sArchive, $sDestination];
    }

    private function removeTestTree(string $sDirectory): void
    {
        if (!is_dir($sDirectory)) {
            return;
        }

        $oIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sDirectory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($oIterator as $oPath) {
            $oPath->isDir() ? rmdir($oPath->getPathname()) : unlink($oPath->getPathname());
        }
        rmdir($sDirectory);
    }
}
