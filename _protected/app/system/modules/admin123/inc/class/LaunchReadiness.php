<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Server\Environment;

final class LaunchReadiness
{
    public const HTTPS = 'https';
    public const INSTALLER_REMOVED = 'installer_removed';
    public const PRODUCTION_MODE = 'production_mode';
    public const SITE_ONLINE = 'site_online';
    public const CRON_SECRET = 'cron_secret';

    public function __construct(
        private readonly string $sSiteUrl,
        private readonly bool $bInstallerPresent,
        private readonly string $sEnvironment,
        private readonly string $sSiteStatus,
        private readonly string $sCronSecurityHash
    ) {
    }

    public function checks(): array
    {
        return [
            self::HTTPS => strtolower((string)parse_url($this->sSiteUrl, PHP_URL_SCHEME)) === 'https',
            self::INSTALLER_REMOVED => !$this->bInstallerPresent,
            self::PRODUCTION_MODE => $this->sEnvironment === Environment::PRODUCTION_MODE,
            self::SITE_ONLINE => $this->sSiteStatus === DbConfig::ENABLED_SITE,
            self::CRON_SECRET => trim($this->sCronSecurityHash) !== ''
        ];
    }

    public function completedCount(): int
    {
        return count(array_filter($this->checks()));
    }

    public function totalCount(): int
    {
        return count($this->checks());
    }

    public function isComplete(): bool
    {
        return $this->completedCount() === $this->totalCount();
    }
}
