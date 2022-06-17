<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security;

use PH7\Framework\Security\InvalidAlgorithmException;
use PH7\Framework\Security\Security;
use PHPUnit\Framework\TestCase;

final class SecurityTest extends TestCase
{
    public function testUserHashWithInvalidAlgorithm(): void
    {
        $this->expectException(InvalidAlgorithmException::class);

        Security::userHash('my nice string', 40, 'wrongAlgo');
    }

    public function testUserHashWithWhirlpoolAlgorithm(): void
    {
        $sActualHash = Security::userHash('my nice string', 48, Security::WHIRLPOOL_ALGORITHM);
        $this->assertSame('33b041b1faf2fcf6515509bdc207fd8fe6e9d2bf182f609d', $sActualHash);
    }

    public function testUserHashWithSha512Algorithm(): void
    {
        $sActualHash = Security::userHash('my lovely string', 30, Security::SHA512_ALGORITHM);
        $this->assertSame('bcd841a3456ccf8a6381b58a7aab75', $sActualHash);
    }

    public function testHash(): void
    {
        $sActualHash = Security::hash('blablabla ...');
        $sExpectedHash = '19fe127eb53178ab6b0c576bcfe90d41225443c6064139e6a058b4b0a4eb040ef912a89dd97c3fb6';
        $this->assertSame($sExpectedHash, $sActualHash);
    }
}
