<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2017-2025, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Service / SearchImage
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Service\SearchImage;

use PH7\Framework\Service\SearchImage\InvalidUrlException;
use PH7\Framework\Service\SearchImage\Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testValidValue(): void
    {
        $sTestUrl = 'https://ph7builder.com/dating-startup-by-step/';
        $oUrl = new Url($sTestUrl);
        $this->assertSame($sTestUrl, $oUrl->getValue());
    }

    #[DataProvider('invalidUrlsProvider')]
    public function testInvalidValue(string $sUrl): void
    {
        $this->expectException(InvalidUrlException::class);

        new Url($sUrl);
    }

    public static function invalidUrlsProvider(): array
    {
        return [
            ['blablabla'],
            [''],
            ['http://' . str_pad('abc', FILTER_VALIDATE_URL * 3, 'abc') . '.com']
        ];
    }
}
