<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\Tool;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PHPUnit_Framework_TestCase;

class ToolTest extends PHPUnit_Framework_TestCase
{
    /** @var HttpRequest */
    private $oHttpRequest;

    /** Config */
    private $oConfig;

    protected function setUp()
    {
        $this->oHttpRequest = new HttpRequest();
        $this->oConfig = Config::getInstance();

        $this->oConfig->values['ph7cms.api']['private_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $this->oConfig->values['ph7cms.api']['allow_domains'][] = 'ph7cms.com';
    }

    /**
     * Make sure the constant value doesn't get changed by mistake...
     *
     * @return void
     */
    public function testSoftwareApiUrl()
    {
        $this->assertSame('http://api.ph7cms.com/', Tool::SOFTWARE_API_URL);
    }

    public function testValidGetApiAccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_GET['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testValidPostApiAccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_POST['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testInvalidUrlApiAccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_POST['url'] = 'wrong-domain.com';

        $this->assertFalse(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testInvalidApiKeyApiAccess()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'wrong_api_key';
        $_POST['url'] = 'ph7cms.com';

        $this->assertFalse(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testDevApiKeyApiAccess()
    {
        $_SERVER['SERVER_NAME'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'dev772277';
        $_POST['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }
}
