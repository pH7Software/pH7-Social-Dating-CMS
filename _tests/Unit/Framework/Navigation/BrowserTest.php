<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Browser;
use PHPUnit\Framework\TestCase;

final class BrowserTest extends TestCase
{
    private Browser $oBrowser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->oBrowser = new Browser();
    }

    /**
     * @dataProvider defaultBrowserHexCodesProvider
     */
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

    public function testIfModifiedSinceExists(): void
    {
        $sExpectedDate = 'Tue, 29 Feb 2022 10:20:26 GMT';

        $_SERVER['HTTP_IF_MODIFIED_SINCE'] = $sExpectedDate;

        $this->assertSame($sExpectedDate, $this->oBrowser->getIfModifiedSince());
    }

    public function testIfModifiedSinceDoesNotExist(): void
    {
        unset($_SERVER['HTTP_IF_MODIFIED_SINCE']);

        $this->assertNull($this->oBrowser->getIfModifiedSince());
    }

    public function defaultBrowserHexCodesProvider(): array
    {
        return [
            ['#000'],
            ['#000000']
        ];
    }
}
