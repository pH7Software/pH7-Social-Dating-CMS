<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2020-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Browser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BrowserTest extends TestCase
{
    private Browser $oBrowser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oBrowser = new Browser();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupGlobalServerVars();
    }

    #[DataProvider('defaultBrowserHexCodesProvider')]
    public function testFoundDefaultBrowserHexCode(string $sHexCode): void
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound($sHexCode);

        $this->assertTrue($bResult);
    }

    public function testNotFoundDefaultBrowserHexCode(): void
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound('#FFF');

        $this->assertFalse($bResult);
    }

    public function testUserAgentSet(): void
    {
        $sExpectedUserAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10)';

        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10)';

        $this->assertSame($sExpectedUserAgent, $this->oBrowser->getUserAgent());
    }

    public function testUserAgentUnset(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = '';

        $this->assertNull($this->oBrowser->getUserAgent());
    }

    public function testHttpRefererSet(): void
    {
        $sExpectedReferer = 'https://ph7builder.com';

        $_SERVER['HTTP_REFERER'] = $sExpectedReferer;

        $this->assertSame($sExpectedReferer, $this->oBrowser->getHttpReferer());
    }

    public function testHttpRefererUnset(): void
    {
        $this->assertNull($this->oBrowser->getHttpReferer());
    }

    public function testIfModifiedSinceHeaderExists(): void
    {
        $sExpectedDate = 'Tue, 29 Feb 2022 08:16:20 GMT';

        $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $sExpectedDate;

        $this->assertSame($sExpectedDate, $this->oBrowser->getIfModifiedSince());
    }

    public function testIfModifiedSinceHeaderDoesNotExist(): void
    {
        $this->assertNull($this->oBrowser->getIfModifiedSince());
    }

    #[DataProvider('encodingServerHeadersProvider')]
    public function testGetEncodingType(string $sEncodingType): void
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = $sEncodingType;

        $this->assertSame($sEncodingType, $this->oBrowser->encoding());
    }

    public function testInvalidEncoding(): void
    {
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'wrong encoding type';

        $this->assertFalse($this->oBrowser->encoding());
    }

    public function testMissingAcceptEncodingDoesNotThrow(): void
    {
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);

        $this->assertFalse($this->oBrowser->encoding());
    }

    public function testMissingAcceptLanguageReturnsEmptyLanguage(): void
    {
        unset($_SERVER['HTTP_ACCEPT_LANGUAGE']);

        $this->assertSame('', $this->oBrowser->getLanguage());
    }

    #[DataProvider('mobileServerHeadersProvider')]
    public function testIsMobile(string $sServerKeyName, string $sServerValue): void
    {
        $_SERVER[$sServerKeyName] = $sServerValue;

        $this->assertTrue($this->oBrowser->isMobile());
    }

    public function testIsNotMobile(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = 'Windows ...';

        $this->assertFalse($this->oBrowser->isMobile());
    }

    public function testFavicon(): void
    {
        $sActual = Browser::favicon('https://ph7cms.com');
        $sExpected = 'https://www.google.com/s2/favicons?domain=ph7cms.com';

        $this->assertSame($sExpected, $sActual);
    }

    public static function defaultBrowserHexCodesProvider(): array
    {
        return [
            ['#000'],
            ['#000000']
        ];
    }

    public static function encodingServerHeadersProvider(): array
    {
        return [
            ['gzip'],
            ['x-gzip'],
        ];
    }

    public static function mobileServerHeadersProvider(): array
    {
        return [
            ['HTTP_X_WAP_PROFILE', 'something'],
            ['HTTP_PROFILE', 'something'],
            ['HTTP_USER_AGENT', 'Mobile'],
            ['HTTP_USER_AGENT', 'Phone'],
            ['HTTP_USER_AGENT', 'iPhone OS'],
            ['HTTP_USER_AGENT', 'Android 123'],
            ['HTTP_USER_AGENT', 'My Opera Mini 000'],
        ];
    }

    private function cleanupGlobalServerVars(): void
    {
        unset($_SERVER['HTTP_ACCEPT_ENCODING']);
        unset($_SERVER['HTTP_X_WAP_PROFILE']);
        unset($_SERVER['HTTP_PROFILE']);
        unset($_SERVER['HTTP_USER_AGENT']);
        unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);
        unset($_SERVER['HTTP_REFERER']);
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en-GB,en;q=0.9';
    }
}
