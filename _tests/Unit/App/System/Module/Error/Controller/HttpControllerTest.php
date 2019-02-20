<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Error / Controller
 */

namespace PH7\Test\Unit\App\System\Module\Error\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit_Framework_TestCase;

class HttpControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var Client */
    protected $oClient;

    protected function setUp()
    {
        $this->oClient = new Client(['exceptions' => false]);
    }

    public function testNotFoundPage()
    {
        $oResponse = $this->oClient->get($this->getUrl());

        $this->assertSame(404, $oResponse->getStatusCode());
        $this->assertRegExp('/Page Not Found/', $oResponse->getBody()->__toString());
    }

    public function testBadRequestPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(400));

        $this->assertSame(400, $oResponse->getStatusCode());
        $this->assertRegExp('/Bad Request/', $oResponse->getBody()->__toString());
    }

    public function testUnauthorizedPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(401));

        $this->assertSame(401, $oResponse->getStatusCode());
        $this->assertRegExp('/Unauthorized/', $oResponse->getBody()->__toString());
    }

    public function testPaymentRequiredPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(402));

        $this->assertSame(402, $oResponse->getStatusCode());
        $this->assertRegExp('/Payment Required/', $oResponse->getBody()->__toString());
    }

    public function testForbiddenPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(403));

        $this->assertSame(403, $oResponse->getStatusCode());
        $this->assertRegExp('/Forbidden/', $oResponse->getBody()->__toString());
    }

    public function testMethodNotAllowedPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(405));

        $this->assertSame(405, $oResponse->getStatusCode());
        $this->assertRegExp('/Method Not Allowed/', $oResponse->getBody()->__toString());
    }

    public function testInternalServerErrorPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(500));

        $this->assertSame(500, $oResponse->getStatusCode());
        $this->assertRegExp('/Internal Server Error/', $oResponse->getBody()->__toString());
    }

    public function testNotImplementedPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(501));

        $this->assertSame(501, $oResponse->getStatusCode());
        $this->assertRegExp('/Not Implemented/', $oResponse->getBody()->__toString());
    }

    public function testBadGatewayPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(502));

        $this->assertSame(502, $oResponse->getStatusCode());
        $this->assertRegExp('/Bad Gateway/', $oResponse->getBody()->__toString());
    }

    public function testGatewayTimeoutPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(504));

        $this->assertSame(504, $oResponse->getStatusCode());
        $this->assertRegExp('/Gateway Timeout/', $oResponse->getBody()->__toString());
    }

    public function testHttpVersionNotSupportedPage()
    {
        $oResponse = $this->oClient->get($this->getUrl(505));

        $this->assertSame(505, $oResponse->getStatusCode());
        $this->assertRegExp('/HTTP Version Not Supported/', $oResponse->getBody()->__toString());
    }

    private function getUrl($sUri = '')
    {
        return Uri::get('error', 'http', 'index', $sUri);
    }
}
