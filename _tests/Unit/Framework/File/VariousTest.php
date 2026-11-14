<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\File;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\Various;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VariousTest extends TestCase
{
    #[DataProvider('sizesProvider')]
    public function testSizeToBytes(string $sSize, int $iExpectedBytes): void
    {
        $this->assertSame($iExpectedBytes, Various::sizeToBytes($sSize));
    }

    public function testInvalidSizeSuffixIsRejected(): void
    {
        $this->expectException(PH7InvalidArgumentException::class);

        Various::sizeToBytes('10T');
    }

    public function testBytesToSizeReturnsTranslatedText(): void
    {
        $this->assertNotSame('', Various::bytesToSize(1024));
        $this->assertNotSame('', Various::bytesToSize(1.5 * 1024 * 1024, 1));
    }

    public static function sizesProvider(): array
    {
        return [
            ['10K', 10 * 1024],
            ['2m', 2 * 1024 ** 2],
            ['1G', 1024 ** 3]
        ];
    }
}
