<?php

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AlbumOwnershipGuardTest extends TestCase
{
    #[DataProvider('mediaUploadProvider')]
    public function testAlbumOwnershipIsCheckedBeforeMediaIsPersisted(
        string $sProcessorPath,
        string $sModelPath,
        string $sPersistMethod,
        string $sAlbumTable
    ): void {
        $sProcessor = $this->readProjectFile($sProcessorPath);
        $sModel = $this->readProjectFile($sModelPath);

        $iGuardPosition = strpos($sProcessor, '->doesAlbumBelongToProfile(');
        $iPersistPosition = strpos($sProcessor, '->' . $sPersistMethod . '(');

        $this->assertIsInt($iGuardPosition);
        $this->assertIsInt($iPersistPosition);
        $this->assertLessThan($iPersistPosition, $iGuardPosition);
        $this->assertStringContainsString('function doesAlbumBelongToProfile(', $sModel);
        $this->assertStringContainsString('DbTableName::' . $sAlbumTable, $sModel);
        $this->assertStringContainsString(
            'WHERE profileId = :profileId AND albumId = :albumId',
            $sModel
        );
    }

    public static function mediaUploadProvider(): array
    {
        return [
            'picture' => [
                'picture/forms/processing/PictureFormProcess.php',
                'picture/models/PictureModel.php',
                'addPhoto',
                'ALBUM_PICTURE'
            ],
            'video' => [
                'video/forms/processing/VideoFormProcess.php',
                'video/models/VideoModel.php',
                'addVideo',
                'ALBUM_VIDEO'
            ]
        ];
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(PH7_PATH_SYS_MOD . $sRelativePath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
