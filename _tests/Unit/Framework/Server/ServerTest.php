<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Server
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Server;

use PH7\Framework\Server\Server;
use PHPUnit\Framework\TestCase;

final class ServerTest extends TestCase
{
    public function testGetServerName(): void
    {
        $_SERVER['SERVER_NAME'] = 'ph7cms.com';

        $this->assertSame('ph7cms.com', Server::getName());
    }

    public function testNotFoundServerName(): void
    {
        $this->assertNull(Server::getName());
    }

    public function testItIsLocalHost(): void
    {
        $_SERVER['SERVER_NAME'] = '127.0.0.1';

        $this->assertTrue(Server::isLocalHost());
    }

    public function testItIsNotLocalHost(): void
    {
        $_SERVER['HTTP_HOST'] = 'ph7cms.com';

        $this->assertFalse(Server::isLocalHost());
    }

    public function testGetUndefinedServerKey(): void
    {
        $sActual = Server::getVar('UNDEFINED');

        $this->assertNull($sActual);
    }

    public function testGetUndefinedServerKeyWithDefaultValue(): void
    {
        $sActual = Server::getVar('UNDEFINED', 'My default value');

        $this->assertSame('My default value', $sActual);
    }

    public function testGetDefinedServerKey(): void
    {
        $_SERVER['SOMETHING'] = "<b>I'm the value</b>";
        $sActual = Server::getVar('SOMETHING');

        $this->assertSame('&lt;b&gt;I&#039;m the value&lt;/b&gt;', $sActual);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cleanupGlobalServerVars();
    }

    private function cleanupGlobalServerVars(): void
    {
        unset($_SERVER['SERVER_NAME']);
        unset($_SERVER['HTTP_HOST']);
    }
}
