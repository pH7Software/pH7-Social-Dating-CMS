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

    private string $sPrivateApiKey;

    private string $sAllowedDomain;

    protected function setUp(): void
    {
        $this->oClient = new Client(['http_errors' => false]);

        $aConfig = parse_ini_file(PH7_PATH_APP_CONFIG . PH7_CONFIG_FILE, true);
        if (!is_array($aConfig) || !isset($aConfig['ph7cms.api'])) {
            $this->markTestSkipped('An installed pH7Builder configuration is required for the API integration tests.');
        }

        $aApiConfig = $aConfig['ph7cms.api'];
        $aAllowedDomains = (array)($aApiConfig['allow_domains'] ?? []);
        $this->sPrivateApiKey = (string)($aApiConfig['private_key'] ?? '');
        $this->sAllowedDomain = (string)reset($aAllowedDomains);

        if ($this->sPrivateApiKey === '' || $this->sAllowedDomain === '') {
            $this->markTestSkipped('API credentials and an allowed domain are required for the API integration tests.');
        }
    }

    public function testDenyRequest(): void
    {
        $oResponse = $this->oClient->get($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => $this->sPrivateApiKey,
                'url' => 'doesntexist.com'
            ]
        ]);

        $this->assertSame(StatusCode::FORBIDDEN, $oResponse->getStatusCode());
    }

    public function testWrongTestRequestMethod(): void
    {
        $oResponse = $this->oClient->post($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => $this->sPrivateApiKey,
                'url' => $this->sAllowedDomain
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
                'private_api_key' => $this->sPrivateApiKey,
                'url' => $this->sAllowedDomain
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
