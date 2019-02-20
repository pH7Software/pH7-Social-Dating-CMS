<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Api / Controller
 */

namespace PH7\Test\Unit\App\System\Module\Api\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit_Framework_TestCase;

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
        $oResponse = $this->oClient->get($this->getApiUrl('test'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'doesntexist.com'
            ]
        ]);

        $this->assertSame(403, $oResponse->getStatusCode());
    }

    public function testWrongTestRequestMethod()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('test'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(406, $oResponse->getStatusCode());
        $this->assertNull(json_decode($oResponse->getBody()));
    }

    public function testNotFoundRequest()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('blablabla'));

        // If website is on development mode, it will return "500" code, otherwise, "404"
        $this->assertRegExp('/404|500/', (string)$oResponse->getStatusCode());
    }

    public function testCorrectTestUri()
    {
        $oResponse = $this->oClient->post($this->getApiUrl('test'), [
            'query' => [
                'private_api_key' => 'dev772277',
                'url' => 'ph7cms.com'
            ]
        ]);

        $this->assertSame(200, $oResponse->getStatusCode());
        $this->assertSame(['return' => 'It Works!'], json_decode($oResponse->getBody(), true));
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
