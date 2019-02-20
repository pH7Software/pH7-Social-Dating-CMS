<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Test / Unit / Framework / Ip
 */

namespace PH7\Test\Unit\Framework\Ip;

use PH7\Framework\Ip\Ip;
use PHPUnit_Framework_TestCase;

class IpTest extends PHPUnit_Framework_TestCase
{
    public function testInvalidIpAddress()
    {
        $_SERVER['REMOTE_ADDR'] = '122222';

        // When it's an invalid IP, it musts return "127.0.0.1" instead
        $this->assertSame('127.0.0.1', Ip::get());
    }

    public function testPrivateIpAddress()
    {
        $_SERVER['REMOTE_ADDR'] = '172.16.0.0';

        // When it's private IP, it musts return "127.0.0.1" instead
        $this->assertSame('127.0.0.1', Ip::get());
    }

    public function testValidIpAddress()
    {
        $_SERVER['REMOTE_ADDR'] = '108.170.3.142';
        $this->assertSame('108.170.3.142', Ip::get());
    }

    public function testIsPrivate()
    {
        $this->assertTrue(Ip::isPrivate('192.168.0.0'));
    }

    public function testIsNotPrivate()
    {
        $this->assertFalse(Ip::isPrivate('52.53.189.95'));
    }
}
