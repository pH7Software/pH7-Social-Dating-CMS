<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Api / Controller
 */

declare(strict_types=1);

namespace PH7\Test\Unit\App\System\Module\Api\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Mvc\Router\Uri;
use PH7\JustHttp\StatusCode;
use PHPUnit\Framework\TestCase;

class MainControllerTest extends TestCase
{
    protected Client $oClient;

    protected function setUp(): void
    {
        $this->oClient = new Client(['exceptions' => false]);
    }

    public function testDenyRequest(): void
    {
        $oResponse = $this->oClient->get($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'doesntexist.com'
            ]
        ]);

        $this->assertSame(StatusCode::FORBIDDEN, $oResponse->getStatusCode());
    }

    public function testWrongTestRequestMethod(): void
    {
        $oResponse = $this->oClient->post($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(StatusCode::NOT_ACCEPTABLE, $oResponse->getStatusCode());
        $this->assertNull(json_decode((string)$oResponse->getBody()));
    }

    public function testNotFoundRequest(): void
    {
        $oResponse = $this->oClient->get($this->getApiUrl('blablabla'));

        // If website is on development mode, it will return "500" code, otherwise, "404"
        $this->assertMatchesRegularExpression('/404|500/', (string)$oResponse->getStatusCode());
    }

    public function testCorrectTestUri(): void
    {
        $oResponse = $this->oClient->get($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(StatusCode::OK, $oResponse->getStatusCode());
        $this->assertSame(['return' => 'Pong'], json_decode((string)$oResponse->getBody(), true));
    }

    /**
     * @param string $sAction The action name.
     * @param string $sController The controller name.
     *
     * @return string
     */
    protected function getApiUrl(string $sAction, string $sController = 'main'): string
    {
        return Uri::get('api', $sController, $sAction);
    }
}
