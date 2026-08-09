<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Core\Classes;

require_once PH7_PATH_SYS . 'core/classes/WriteCore.php';

use PH7\WriteCore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

final class WriteCoreThumbnailPathTest extends TestCase
{
    #[DataProvider('validThumbnailPathProvider')]
    public function testValidThumbnailIdentifiersAreNormalized(int|string $mId, string $sModule, string $sExpected): void
    {
        $oMethod = new ReflectionMethod(WriteCore::class, 'normalizeThumbPath');

        $this->assertSame($sExpected, $oMethod->invoke(null, $mId, $sModule));
    }

    public static function validThumbnailPathProvider(): array
    {
        return [
            'blog integer ID' => [42, 'blog', '42' . PH7_DS . WriteCore::THUMBNAIL_FILENAME],
            'blog digit ID' => ['42', 'blog', '42' . PH7_DS . WriteCore::THUMBNAIL_FILENAME],
            'note path' => ['alice' . PH7_DS . 'thumb.png', 'note', 'alice' . PH7_DS . 'thumb.png']
        ];
    }

    #[DataProvider('invalidThumbnailPathProvider')]
    public function testUnsafeThumbnailIdentifiersAreRejected(int|string $mId, string $sModule): void
    {
        $oMethod = new ReflectionMethod(WriteCore::class, 'normalizeThumbPath');

        $this->assertNull($oMethod->invoke(null, $mId, $sModule));
    }

    public static function invalidThumbnailPathProvider(): array
    {
        return [
            'blog traversal' => ['../42', 'blog'],
            'blog Windows traversal' => ['..\\42', 'blog'],
            'blog zero' => [0, 'blog'],
            'blog negative' => [-1, 'blog'],
            'blog leading zero' => ['042', 'blog'],
            'blog decimal' => ['1.0', 'blog'],
            'note parent username' => ['..' . PH7_DS . 'thumb.png', 'note'],
            'note parent thumbnail' => ['alice' . PH7_DS . '..', 'note'],
            'note extra segment' => ['alice' . PH7_DS . 'nested' . PH7_DS . 'thumb.png', 'note'],
            'note Windows traversal' => ['alice\\..\\thumb.png', 'note'],
            'note empty username' => [PH7_DS . 'thumb.png', 'note'],
            'note NUL byte' => ["alice" . PH7_DS . "thumb\0.png", 'note'],
            'note integer' => [42, 'note']
        ];
    }
}
