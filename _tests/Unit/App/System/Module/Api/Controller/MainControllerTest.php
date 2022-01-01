<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Api / Controller
 */

namespace PH7\Test\Unit\App\System\Module\Api\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit_Framework_TestCase;
use PH7\JustHttp\StatusCode;

class MainControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var Client */
    protected $oClient;

    protected function setUp()
    {
        $this->oClient = new Client(['exceptions' => false]);
    }

    public function testDenyRequest()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'doesntexist.com'
            ]
        ]);

        $this->assertSame(StatusCode::FORBIDDEN, $oResponse->getStatusCode());
    }

    public function testWrongTestRequestMethod()
    {
        $oResponse = $this->oClient->post($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(StatusCode::NOT_ACCEPTABLE, $oResponse->getStatusCode());
        $this->assertNull(json_decode($oResponse->getBody()));
    }

    public function testNotFoundRequest()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('blablabla'));

        // If website is on development mode, it will return "500" code, otherwise, "404"
        $this->assertMatchesRegularExpression('/404|500/', (string)$oResponse->getStatusCode());
    }

    public function testCorrectTestUri()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('ping'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(StatusCode::OK, $oResponse->getStatusCode());
        $this->assertSame(['return' => 'Pong'], json_decode($oResponse->getBody(), true));
    }

    /**
     * @param string $sAction The action name.
     * @param string $sController The controller name.
     *
     * @return string
     */
    protected function getApiUrl($sAction, $sController = 'main')
    {
        return Uri::get('api', $sController, $sAction);
    }
}
