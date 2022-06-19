<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Ip
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Ip;

use PH7\Framework\Ip\Ip;
use PHPUnit\Framework\TestCase;

final class IpTest extends TestCase
{
    public function testInvalidIpAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '122222';

        // When it's an invalid IP, it must return "127.0.0.1" instead
        $this->assertSame('127.0.0.1', Ip::get());
    }

    public function testPrivateIpAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '172.16.0.0';

        // When it's private IP, it must return "127.0.0.1" instead
        $this->assertSame('127.0.0.1', Ip::get());
    }

    public function testValidIpAddress(): void
    {
        $_SERVER['REMOTE_ADDR'] = '108.170.3.142';
        $this->assertSame('108.170.3.142', Ip::get());
    }

    public function testIsPrivate(): void
    {
        $this->assertTrue(Ip::isPrivate('192.168.0.0'));
    }

    public function testIsNotPrivate(): void
    {
        $this->assertFalse(Ip::isPrivate('52.53.189.95'));
    }
}
