<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Url;

use PH7\Framework\Url\Url;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testEncode(): void
    {
        $sUrl = 'https://ph7cms.com/my-route & the new_2£POST!';
        $sExpected = 'https%3A%2F%2Fph7cms.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';

        $this->assertSame($sExpected, Url::encode($sUrl));
    }

    public function testDecode(): void
    {
        $sEncodedUrl = 'https%3A%2F%2Fph7cms.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';
        $sExpected = 'https://ph7cms.com/my-route & the new_2£POST!';

        $this->assertSame($sExpected, Url::decode($sEncodedUrl));
    }

    /**
     * @dataProvider urlsProvider
     */
    public function testClean(string $sActualUrl, string $sExpectedUrl)
    {
        $this->assertSame($sExpectedUrl, Url::clean($sActualUrl));
    }

    public function urlsProvider(): array
    {
        return [
            ['https://ph7cms.com/my post is this one', 'https://ph7cms.com/my%20post%20is%20this%20one'],
            ['https://ph7cms.com/?myparam=var&var2=value and value2', 'https://ph7cms.com/?myparam=var&amp;var2=value%20and%20value2']
        ];
    }
}
