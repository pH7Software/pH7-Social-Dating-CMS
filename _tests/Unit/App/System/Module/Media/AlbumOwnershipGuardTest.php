<?php

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Media;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AlbumOwnershipGuardTest extends TestCase
{
    private const PROJECT_ROOT = __DIR__ . '/../../../../../../';

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
                '_protected/app/system/modules/picture/forms/processing/PictureFormProcess.php',
                '_protected/app/system/modules/picture/models/PictureModel.php',
                'addPhoto',
                'ALBUM_PICTURE'
            ],
            'video' => [
                '_protected/app/system/modules/video/forms/processing/VideoFormProcess.php',
                '_protected/app/system/modules/video/models/VideoModel.php',
                'addVideo',
                'ALBUM_VIDEO'
            ]
        ];
    }

    private function readProjectFile(string $sRelativePath): string
    {
        $sContents = file_get_contents(self::PROJECT_ROOT . $sRelativePath);
        $this->assertIsString($sContents);

        return $sContents;
    }
}
