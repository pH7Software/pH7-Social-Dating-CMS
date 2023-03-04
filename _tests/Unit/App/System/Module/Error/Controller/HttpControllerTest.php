<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Error / Controller
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Error\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit\Framework\TestCase;

final class HttpControllerTest extends TestCase
{
    protected Client $oClient;

    protected function setUp(): void
    {
        $this->oClient = new Client(['exceptions' => false]);
    }

    public function testNotFoundPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl());

        $this->assertSame(404, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Page Not Found/', $oResponse->getBody()->__toString());
    }

    public function testBadRequestPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('400'));

        $this->assertSame(400, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Bad Request/', $oResponse->getBody()->__toString());
    }

    public function testUnauthorizedPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('401'));

        $this->assertSame(401, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Unauthorized/', $oResponse->getBody()->__toString());
    }

    public function testPaymentRequiredPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('402'));

        $this->assertSame(402, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Payment Required/', $oResponse->getBody()->__toString());
    }

    public function testForbiddenPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('403'));

        $this->assertSame(403, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Forbidden/', $oResponse->getBody()->__toString());
    }

    public function testMethodNotAllowedPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('405'));

        $this->assertSame(405, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Method Not Allowed/', $oResponse->getBody()->__toString());
    }

    public function testInternalServerErrorPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('500'));

        $this->assertSame(500, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Internal Server Error/', $oResponse->getBody()->__toString());
    }

    public function testNotImplementedPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('501'));

        $this->assertSame(501, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Not Implemented/', $oResponse->getBody()->__toString());
    }

    public function testBadGatewayPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('502'));

        $this->assertSame(502, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Bad Gateway/', $oResponse->getBody()->__toString());
    }

    public function testGatewayTimeoutPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('504'));

        $this->assertSame(504, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/Gateway Timeout/', $oResponse->getBody()->__toString());
    }

    public function testHttpVersionNotSupportedPage(): void
    {
        $oResponse = $this->oClient->get($this->getUrl('505'));

        $this->assertSame(505, $oResponse->getStatusCode());
        $this->assertMatchesRegularExpression('/HTTP Version Not Supported/', $oResponse->getBody()->__toString());
    }

    private function getUrl(string $sUri = ''): string
    {
        return Uri::get('error', 'http', 'index', $sUri);
    }
}
