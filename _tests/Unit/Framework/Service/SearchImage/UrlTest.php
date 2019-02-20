<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Service / SearchImage
 */

namespace PH7\Test\Unit\Framework\Service\SearchImage;

use PH7\Framework\Service\SearchImage\Url;
use PHPUnit_Framework_TestCase;

class UrlTest extends PHPUnit_Framework_TestCase
{
    public function testValidValue()
    {
        $sTestUrl = 'https://ph7cms.com/dating-business-by-steps/';
        $oUrl = new Url($sTestUrl);
        $this->assertSame($sTestUrl, $oUrl->getValue());
    }

    /**
     * @param string $sUrl
     *
     * @dataProvider invalidUrlsProvider
     *
     * @expectedException \PH7\Framework\Service\SearchImage\InvalidUrlException
     */
    public function testInvalidValue($sUrl)
    {
        new Url($sUrl);
    }

    /**
     * @return array
     */
    public function invalidUrlsProvider()
    {
        return [
            ['blablabla'],
            [''],
            ['http://' . str_pad('abc', FILTER_VALIDATE_URL * 3, 'abc') . '.com']
        ];
    }
}
