<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / App / System / Module / Api / Controller
 */

namespace PH7\Test\Unit\App\System\Module\Api\Controller;

use GuzzleHttp\Client;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PHPUnit_Framework_TestCase;

class MainControllerTest extends PHPUnit_Framework_TestCase
{
    /** @var HttpRequest */
    protected $oHttpRequest;

    /** Config */
    protected $oConfig;

    /** @var Client */
    protected $oClient;

    protected function setUp()
    {
        $this->oHttpRequest = new HttpRequest();
        $this->oConfig = Config::getInstance();

        $this->oConfig->values['ph7cms.api']['private_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $this->oConfig->values['ph7cms.api']['allow_domains'][] = 'ph7cms.com';

        $this->oClient = new Client(['exceptions' => false]);
    }

    public function testDenyRequest()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('test'), [
            'private_api_key' => 'wrong api key',
            'url' => 'doesntexist.com'
        ]);

        $this->assertSame(403, $oResponse->getStatusCode());
    }

    public function testWrongTestRequestMethod()
    {
        $oResponse = $this->oClient->get($this->getApiUrl('test'), [
            'private_api_key' => 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7',
             'url' => 'ph7cms.com'
        ]);

        $this->assertSame(406, $oResponse->getStatusCode());
        $this->assertSame('', $oResponse->getBody());
    }

    public function testCorrectTestUri()
    {
        $oResponse = $this->oClient->post($this->getApiUrl('test'), [
            'private_api_key' => 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7',
            'url' => 'ph7cms.com'
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