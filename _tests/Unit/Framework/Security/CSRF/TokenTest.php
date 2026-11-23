<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Framework / Security / CSRF
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security\CSRF;

@session_start();

use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Session\Session;
use PHPUnit\Framework\TestCase;

final class TokenTest extends TestCase
{
    private const TOKEN_NAME = 'unit_test';

    protected function setUp(): void
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['HTTP_USER_AGENT'] = 'pH7Builder CSRF regression test';
    }

    protected function tearDown(): void
    {
        (new Session())->remove([
            'security_token_' . self::TOKEN_NAME,
            'security_token_time_' . self::TOKEN_NAME,
            'security_token_ip_' . self::TOKEN_NAME,
            'security_token_http_user_agent_' . self::TOKEN_NAME
        ]);

        unset($_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
    }

    public function testGeneratedTokenCanOnlyBeValidatedOnce(): void
    {
        $oToken = new Token();
        $sToken = $oToken->generate(self::TOKEN_NAME);

        $this->assertTrue($oToken->check(self::TOKEN_NAME, $sToken, 60));
        $this->assertFalse($oToken->check(self::TOKEN_NAME, $sToken, 60));
    }

    public function testIncorrectTokenIsRejected(): void
    {
        $oToken = new Token();
        $oToken->generate(self::TOKEN_NAME);

        $this->assertFalse($oToken->check(self::TOKEN_NAME, str_repeat('0', 40), 60));
    }
}
