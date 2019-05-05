<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Http
 */

namespace PH7\Test\Unit\Framework\Http;

use PH7\Framework\Http\Http;
use PHPUnit_Framework_TestCase;

class HttpTest extends PHPUnit_Framework_TestCase
{
    /** @var Http */
    private $oHttp;

    protected function setUp()
    {
        $this->oHttp = new Http;
    }

    public function testValidStatusCode()
    {
        $mActual = Http::getStatusCode(200);

        $this->assertSame('200 OK', $mActual);
    }

    public function testInvalidStatusCode()
    {
        $mActual = Http::getStatusCode(50505);

        $this->assertFalse($mActual);
    }

    public function testRelativeUrl()
    {
        $bActual = $this->oHttp->isRelativeUrl('https://pierrehenry.be/user/brows');

        $this->assertFalse($bActual);
    }

    public function testNotRelativeUrl()
    {
        $bActual = $this->oHttp->isRelativeUrl('/user/browse');

        $this->assertTrue($bActual);
    }
}
