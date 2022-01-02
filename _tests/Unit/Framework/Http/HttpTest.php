<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Http
 */

namespace PH7\Test\Unit\Framework\Http;

use PH7\Framework\Http\Http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    private Http $oHttp;

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

    protected function setUp(): void
    {
        $this->oHttp = new Http;
    }
}
