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
    protected function tearDown(): void
    {
        unset(
            $_SERVER['HTTP_CF_CONNECTING_IP'],
            $_SERVER['HTTP_TRUE_CLIENT_IP'],
            $_SERVER['HTTP_X_REAL_IP'],
            $_SERVER['HTTP_CLIENT_IP'],
            $_SERVER['HTTP_X_FORWARDED_FOR'],
            $_SERVER['REMOTE_ADDR']
        );
    }

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

    public function testForwardedForListReturnsPublicIp(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.5, 52.53.189.95';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->assertSame('52.53.189.95', Ip::get());
    }

    public function testCloudflareConnectingIpHasPriority(): void
    {
        $_SERVER['HTTP_CF_CONNECTING_IP'] = '104.16.249.249';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '10.0.0.5, 52.53.189.95';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->assertSame('104.16.249.249', Ip::get());
    }

    public function testTrueClientIpHasPriorityOverXForwardedFor(): void
    {
        $_SERVER['HTTP_TRUE_CLIENT_IP'] = '104.18.20.87';
        $_SERVER['HTTP_X_FORWARDED_FOR'] = '198.51.100.10';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->assertSame('104.18.20.87', Ip::get());
    }

    public function testInvalidForwardedForFallsBackToRemoteAddress(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'unknown';
        $_SERVER['REMOTE_ADDR'] = '52.53.189.95';

        $this->assertSame('52.53.189.95', Ip::get());
    }

    public function testForwardedForWithPrivateCandidatesFallsBackToDefaultIp(): void
    {
        $_SERVER['HTTP_X_FORWARDED_FOR'] = 'unknown, 10.0.0.5, 172.16.0.1';
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        $this->assertSame('127.0.0.1', Ip::get());
    }

    public function testValidIpv6Address(): void
    {
        $_SERVER['REMOTE_ADDR'] = '2606:4700:4700::1111';

        $this->assertSame('2606:4700:4700::1111', Ip::get());
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
