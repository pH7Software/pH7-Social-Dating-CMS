<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2017-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Url;

use PH7\Framework\Url\Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testEncode(): void
    {
        $sUrl = 'https://ph7builder.com/my-route & the new_2£POST!';
        $sExpected = 'https%3A%2F%2Fph7builder.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';

        $this->assertSame($sExpected, Url::encode($sUrl));
    }

    public function testDecode(): void
    {
        $sEncodedUrl = 'https%3A%2F%2Fph7builder.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';
        $sExpected = 'https://ph7builder.com/my-route & the new_2£POST!';

        $this->assertSame($sExpected, Url::decode($sEncodedUrl));
    }

    #[DataProvider('urlsProvider')]
    public function testClean(string $sActualUrl, string $sExpectedUrl)
    {
        $this->assertSame($sExpectedUrl, Url::clean($sActualUrl));
    }

    public static function urlsProvider(): array
    {
        return [
            ['https://ph7builder.com/my post is this one', 'https://ph7builder.com/my%20post%20is%20this%20one'],
            ['https://ph7builder.com/?myparam=var&var2=value and value2', 'https://ph7builder.com/?myparam=var&amp;var2=value%20and%20value2']
        ];
    }
}
