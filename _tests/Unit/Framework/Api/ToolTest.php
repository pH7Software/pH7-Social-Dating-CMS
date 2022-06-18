<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Api
 */

namespace PH7\Test\Unit\Framework\Api;

use PH7\Framework\Api\Tool;
use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PHPUnit\Framework\TestCase;

class ToolTest extends TestCase
{
    private HttpRequest $oHttpRequest;

    private Config $oConfig;

    protected function setUp(): void
    {
        $this->oHttpRequest = new HttpRequest();
        $this->oConfig = Config::getInstance();

        $this->oConfig->values['ph7cms.api']['private_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $this->oConfig->values['ph7cms.api']['allow_domains'][] = 'ph7cms.com';
    }

    /**
     * Make sure the constant value doesn't get changed by mistake...
     */
    public function testSoftwareApiUrl(): void
    {
        $this->assertSame('https://api.ph7cms.com/', Tool::SOFTWARE_API_URL);
    }

    public function testValidGetApiAccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_GET['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testValidPostApiAccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_POST['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testInvalidUrlApiAccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'c56cd417b958b9ce37bdd80569ef94836ccdc5c7';
        $_POST['url'] = 'wrong-domain.com';

        $this->assertFalse(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testInvalidApiKeyApiAccess(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'wrong_api_key';
        $_POST['url'] = 'ph7cms.com';

        $this->assertFalse(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }

    public function testDevApiKeyApiAccess(): void
    {
        $_SERVER['SERVER_NAME'] = '127.0.0.1';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['private_api_key'] = 'dev772277';
        $_POST['url'] = 'ph7cms.com';

        $this->assertTrue(Tool::checkAccess($this->oConfig, $this->oHttpRequest));
    }
}
