<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Http
 */

namespace PH7\Test\Unit\Framework\Http;

use PH7\Framework\Http\Http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private Http $oHttp;

    protected function setUp(): void
    {
        $this->oHttp = new Http;
    }

    public function testValidStatusCode(): void
    {
        $mActual = Http::getStatusCode(200);

        $this->assertSame('200 OK', $mActual);
    }

    public function testInvalidStatusCode(): void
    {
        $mActual = Http::getStatusCode(50505);

        $this->assertFalse($mActual);
    }

    public function testRelativeUrl(): void
    {
        $bActual = $this->oHttp->isRelativeUrl('https://pierrehenry.be/user/brows');

        $this->assertFalse($bActual);
    }

    public function testNotRelativeUrl(): void
    {
        $bActual = $this->oHttp->isRelativeUrl('/user/browse');

        $this->assertTrue($bActual);
    }

    /**
     * @dataProvider urlsAndHostNamesProvider
     */
    public function testHostName(string $sUrl, $sExpectedUrl): void
    {
        $sActualUrl = Http::getHostName($sUrl);

        $this->assertSame($sExpectedUrl, $sActualUrl);
    }

    /**
     * @dataProvider  sslHeadersProvider
     */
    public function testIsSsl(string $sServerKeyName, string $sServerValue): void
    {
        $_SERVER[$sServerKeyName] = $sServerValue;

        $sIsSsl = Http::isSsl();

        $this->assertTrue($sIsSsl);
    }

    public function testIsNotSsl(): void
    {
        $_SERVER['SERVER_PORT'] = 80;

        $sIsSsl = Http::isSsl();

        $this->assertFalse($sIsSsl);
    }

    public function urlsAndHostNamesProvider(): array
    {
        return [
            ['https://github.com/pH-7/GoodJsCode/blob/main/readme.md', 'github.com'],
            ['https://www.github.com/pH-7/GoodJsCode/blob/main/readme.md', 'github.com'],
            ['https://ph7.me', 'ph7.me'],
            ['https://www.google.com/search?hl=en&q=%22https%3A%2F%2Fph7cms.com%2Fdoc%2Fen%2Fcode%2Dconvention%23variable%2Dnames%22', 'google.com'],
        ];
    }

    public function sslHeadersProvider(): array
    {
        return [
            ['HTTPS', 'on'],
            ['HTTPS', '1'],
            ['SERVER_PORT', '443'],
        ];
    }
}
