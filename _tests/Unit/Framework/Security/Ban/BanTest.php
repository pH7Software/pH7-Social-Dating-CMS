<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2017-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Security / Ban
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Security\Ban;

use PH7\Framework\Security\Ban\Ban;
use PHPUnit\Framework\TestCase;

final class BanTest extends TestCase
{
    private string $sEmailFilePath;

    private string $sUsernameFilePath;

    private string $sIpFilePath;

    private string $sEmailFileBackup;

    private string $sUsernameFileBackup;

    private string $sIpFileBackup;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sEmailFilePath = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::EMAIL_FILE;
        $this->sUsernameFilePath = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::USERNAME_FILE;
        $this->sIpFilePath = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;

        $this->sEmailFileBackup = (string)file_get_contents($this->sEmailFilePath);
        $this->sUsernameFileBackup = (string)file_get_contents($this->sUsernameFilePath);
        $this->sIpFileBackup = (string)file_get_contents($this->sIpFilePath);

        file_put_contents($this->sEmailFilePath, "@blocked.test\n");
        file_put_contents($this->sUsernameFilePath, "blocked_username\n");
        file_put_contents($this->sIpFilePath, "203.0.113.42\n");
    }

    protected function tearDown(): void
    {
        file_put_contents($this->sEmailFilePath, $this->sEmailFileBackup);
        file_put_contents($this->sUsernameFilePath, $this->sUsernameFileBackup);
        file_put_contents($this->sIpFilePath, $this->sIpFileBackup);

        parent::tearDown();
    }

    public function testUsernameCheckDoesNotReuseEmailDomainState(): void
    {
        $this->assertTrue(Ban::isEmail('user@blocked.test'));
        $this->assertFalse(Ban::isUsername('alice@blocked.test'));
    }

    public function testIpCheckDoesNotReuseEmailDomainState(): void
    {
        $this->assertTrue(Ban::isEmail('user@blocked.test'));
        $this->assertFalse(Ban::isIp('198.51.100.10'));
        $this->assertTrue(Ban::isIp('203.0.113.42'));
    }
}
