<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Navigation
 */

namespace PH7\Test\Unit\Framework\Navigation;

use PH7\Framework\Navigation\Browser;
use PHPUnit_Framework_TestCase;

class BrowserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider defaultBrowserHexCodesProvider
     */
    public function testFoundDefaultBrowserHexCode($sHexCode)
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound($sHexCode);

        $this->assertTrue($bResult);
    }

    public function testNotFoundDefaultBrowserHexCode()
    {
        $bResult = Browser::isDefaultBrowserHexCodeFound('#FFF');

        $this->assertFalse($bResult);
    }

    /**
     * @return array
     */
    public function defaultBrowserHexCodesProvider()
    {
        return [
            ['#000'],
            ['#000000']
        ];
    }
}
