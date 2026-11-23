<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / Test / Unit / Tools / Cli / Migration
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Tools\Cli\Migration;

use PH7\Cli\Migration\UnaMemberMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UnaMemberMapperTest extends TestCase
{
    public function testMapsVerifiedMemberFieldsWithoutCopyingPasswordHash(): void
    {
        $aMember = (new UnaMemberMapper())->map([
            'source_id' => 42,
            'name' => 'nomad-alex',
            'email' => 'Alex@Example.test ',
            'email_confirmed' => 1,
            'account_active' => 1,
            'ip' => '2001:db8::1',
            'profile_status' => 'active',
            'fullname' => 'Alex Morgan',
            'last_name' => 'Morgan',
            'description' => 'Travelling between Wellington and Lisbon.',
            'gender' => 'Man',
            'birthday' => '1990-04-12',
            'password' => 'source-hash-must-not-leave-una'
        ]);

        $this->assertSame('una', $aMember['source_platform']);
        $this->assertSame('14.0.0', $aMember['source_version']);
        $this->assertSame('42', $aMember['source_id']);
        $this->assertSame('alex@example.test', $aMember['email']);
        $this->assertSame('Alex', $aMember['first_name']);
        $this->assertSame('Morgan', $aMember['last_name']);
        $this->assertSame('male', $aMember['sex']);
        $this->assertSame('1990-04-12', $aMember['birth_date']);
        $this->assertSame('2001:db8::1', $aMember['ip']);
        $this->assertSame('1', $aMember['is_active']);
        $this->assertSame('', $aMember['password']);
    }

    #[DataProvider('pendingAccountProvider')]
    public function testKeepsUnverifiedSourceAccountsPending(array $aSource): void
    {
        $aSource += [
            'source_id' => 1,
            'name' => 'member',
            'email' => 'member@example.test',
            'fullname' => 'Example Member',
            'last_name' => 'Member',
            'profile_status' => 'active',
            'email_confirmed' => 1,
            'account_active' => 1
        ];

        $this->assertSame('3', (new UnaMemberMapper())->map($aSource)['is_active']);
    }

    public static function pendingAccountProvider(): iterable
    {
        yield 'unconfirmed email' => [['email_confirmed' => 0]];
        yield 'inactive account' => [['account_active' => 0]];
        yield 'suspended profile' => [['profile_status' => 'suspended']];
    }

    public function testRejectsMalformedDemographicValuesWithoutExecutingThem(): void
    {
        $aMember = (new UnaMemberMapper())->map([
            'source_id' => 8,
            'name' => '',
            'email' => 'solo@example.test',
            'email_confirmed' => 1,
            'account_active' => 1,
            'profile_status' => 'active',
            'fullname' => 'Solo',
            'last_name' => '',
            'gender' => '<script>alert(1)</script>',
            'birthday' => '31/31/2099',
            'ip' => '../../etc/passwd'
        ]);

        $this->assertSame('solo', $aMember['username']);
        $this->assertSame('Solo', $aMember['first_name']);
        $this->assertSame('Member', $aMember['last_name']);
        $this->assertSame('', $aMember['sex']);
        $this->assertSame('', $aMember['birth_date']);
        $this->assertSame('0.0.0.0', $aMember['ip']);
        $this->assertSame('3', $aMember['is_active']);
    }
}
