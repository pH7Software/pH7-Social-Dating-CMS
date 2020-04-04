<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Config
 */

namespace PH7\Test\Unit\Framework\Url;

use PH7\Framework\Url\Url;
use PHPUnit_Framework_TestCase;

class UrlTest extends PHPUnit_Framework_TestCase
{
    public function testEncode()
    {
        $sUrl = 'https://ph7cms.com/my-route & the new_2£POST!';
        $sExpected = 'https%3A%2F%2Fph7cms.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';

        $this->assertSame($sExpected, Url::encode($sUrl));
    }

    public function testDecode()
    {
        $sEncodedUrl = 'https%3A%2F%2Fph7cms.com%2Fmy-route+%26+the+new_2%C2%A3POST%21';
        $sExpected = 'https://ph7cms.com/my-route & the new_2£POST!';

        $this->assertSame($sExpected, Url::decode($sEncodedUrl));
    }

    /**
     * @param string $sActualUrl
     * @param string $sExpectedUrl
     *
     * @dataProvider urlsProvider
     */
    public function testClean($sActualUrl, $sExpectedUrl)
    {
        $this->assertSame($sExpectedUrl, Url::clean($sActualUrl));
    }

    /**
     * @return array
     */
    public function urlsProvider()
    {
        return [
            ['https://ph7cms.com/my post is this one', 'https://ph7cms.com/my%20post%20is%20this%20one'],
            ['https://ph7cms.com/?myparam=var&var2=value and value2', 'https://ph7cms.com/?myparam=var&amp;var2=value%20and%20value2']
        ];
    }
}
