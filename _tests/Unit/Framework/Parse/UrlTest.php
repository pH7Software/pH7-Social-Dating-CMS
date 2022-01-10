<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Parse;

use PH7\Framework\Parse\Url as UrlParser;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{
    public function testClean(): void
    {
        $sUrl = 'https://ph7cms.com/my-route & the new_2£POST!';
        $sExpected = 'https//ph7cmscom/my-route-&-the-new_2-post';

        $this->assertSame($sExpected, UrlParser::clean($sUrl));
    }

    /**
     * @dataProvider urlsNamesProvider
     */
    public function testName(string $sActualUrl, string $sExpectedUrl): void
    {
        $this->assertSame($sExpectedUrl, UrlParser::name($sActualUrl));
    }

    public function urlsNamesProvider(): array
    {
        return [
            ['https://ph7cms.com', 'Ph7cms'],
            ['https://ph7cms.com/dating-business-by-steps/', 'Ph7cms.com/dating-business-by-steps/'],
            ['https://www.ph7cms.com?myparam=value-foo-bar', 'Ph7cms.com?myparam=value-foo-bar']
        ];
    }
}
