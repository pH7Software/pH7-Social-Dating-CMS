<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Compress / ValueObject
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Compress\ValueObject;

use PH7\Framework\Compress\ValueObject\FileType;
use PH7\Framework\Compress\ValueObject\InvalidFileTypeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FileTypeTest extends TestCase
{
    #[DataProvider('validTypesProvider')]
    public function testValidFileType(string $sFileType): void
    {
        $oFileType = new FileType($sFileType);
        $this->assertSame($sFileType, $oFileType->getValue());
    }

    #[DataProvider('invalidTypesProvider')]
    public function testInvalidFileType(string $sFileType): void
    {
        $this->expectException(InvalidFileTypeException::class);

        new FileType($sFileType);
    }

    public static function validTypesProvider(): array
    {
        return [
            ['js'],
            ['css']
        ];
    }

    public static function invalidTypesProvider(): array
    {
        return [
            [''],
            ['php']
        ];
    }
}
