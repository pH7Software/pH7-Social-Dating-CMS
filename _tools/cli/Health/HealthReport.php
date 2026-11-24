<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Cli\Health;

final class HealthReport
{
    public const CURRENT_SCHEMA_VERSION = '1.6.7';

    private const REQUIRED_PHP_VERSION = '8.3.0';
    private const REQUIRED_MYSQL_VERSION = '8.0.0';
    private const DEFAULT_CRON_SECRET = 'change_this_secret_cron_word_by_yours';
    private const STALE_CACHE_SECONDS = 30 * 86400;
    private const REQUIRED_EXTENSIONS = [
        'curl',
        'dom',
        'exif',
        'fileinfo',
        'gd',
        'hash',
        'iconv',
        'json',
        'mbstring',
        'openssl',
        'pdo_mysql',
        'simplexml',
        'xml',
        'xmlwriter',
        'zip',
        'zlib'
    ];

    private readonly \Closure $oDatabaseProbe;
    private readonly string $sMailerDsn;
    private readonly int $iNow;

    public function __construct(
        private readonly string $sProjectRoot,
        ?\Closure $oDatabaseProbe = null,
        ?string $sMailerDsn = null,
        ?int $iNow = null
    ) {
        $this->oDatabaseProbe = $oDatabaseProbe ?? $this->probeDatabase(...);
        $this->sMailerDsn = $sMailerDsn ?? (string)getenv('PH7_MAILER_DSN');
        $this->iNow = $iNow ?? time();
    }

    public function collect(): array
    {
        $aChecks = [];
        $this->addCheck(
            $aChecks,
            'PHP runtime',
            version_compare(PHP_VERSION, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP %s; requires %s or newer.', PHP_VERSION, self::REQUIRED_PHP_VERSION)
        );

        $aMissingExtensions = array_values(
            array_filter(self::REQUIRED_EXTENSIONS, static fn (string $sExtension): bool => !extension_loaded($sExtension))
        );
        $this->addCheck(
            $aChecks,
            'PHP extensions',
            $aMissingExtensions === [],
            $aMissingExtensions === []
                ? 'All required extensions are loaded.'
                : 'Missing: ' . implode(', ', $aMissingExtensions) . '.'
        );

        $sConfigPath = $this->path('_protected/app/configs/config.ini');
        $aConfig = is_file($sConfigPath)
            ? parse_ini_file($sConfigPath, true, INI_SCANNER_TYPED)
            : false;
        if (!is_array($aConfig)) {
            $aChecks[] = $this->check('Installation', 'fail', 'Application configuration is missing or unreadable.');

            return $aChecks;
        }

        $sEnvironment = (string)($aConfig['mode']['environment'] ?? 'unknown');
        $this->addCheck(
            $aChecks,
            'Production mode',
            $sEnvironment === 'production',
            sprintf('Configured environment: %s.', $sEnvironment)
        );
        $this->addCheck(
            $aChecks,
            'Installer lockdown',
            !is_dir($this->path('_install')),
            is_dir($this->path('_install'))
                ? 'Remove or block the _install directory before production use.'
                : 'The installer directory is absent.'
        );

        $aUnwritablePaths = [];
        foreach ($this->runtimePaths() as $sLabel => $sPath) {
            if (!is_dir($sPath) || !is_writable($sPath)) {
                $aUnwritablePaths[] = $sLabel;
            }
        }
        $this->addCheck(
            $aChecks,
            'Runtime paths',
            $aUnwritablePaths === [],
            $aUnwritablePaths === []
                ? 'Required runtime directories are writable.'
                : 'Missing or not writable: ' . implode(', ', $aUnwritablePaths) . '.'
        );

        try {
            $aDatabase = ($this->oDatabaseProbe)($aConfig);
            $sDatabaseVersion = (string)($aDatabase['version'] ?? '');
            $sComparableVersion = $this->normaliseVersion($sDatabaseVersion);
            $this->addCheck(
                $aChecks,
                'MySQL runtime',
                $sComparableVersion !== '' && version_compare($sComparableVersion, self::REQUIRED_MYSQL_VERSION, '>='),
                sprintf('Database server %s; requires MySQL %s or newer.', $sDatabaseVersion, self::REQUIRED_MYSQL_VERSION)
            );

            $sSchemaVersion = (string)($aDatabase['schema'] ?? '');
            $this->addCheck(
                $aChecks,
                'Database schema',
                $sSchemaVersion === self::CURRENT_SCHEMA_VERSION,
                sprintf('Installed %s; required %s.', $sSchemaVersion ?: 'unknown', self::CURRENT_SCHEMA_VERSION)
            );

            $sCronSecret = (string)($aDatabase['cron_secret'] ?? '');
            $bCronConfigured = $sCronSecret !== '' && !hash_equals(self::DEFAULT_CRON_SECRET, $sCronSecret);
            $this->addCheck(
                $aChecks,
                'Cron configuration',
                $bCronConfigured,
                $bCronConfigured
                    ? 'A non-default cron secret is configured; verify scheduler execution externally.'
                    : 'Replace the default cron secret and configure the documented scheduled jobs.'
            );
        } catch (\Throwable $oException) {
            $aChecks[] = $this->check(
                'Database connection',
                'fail',
                'The configured database could not be inspected: ' . $oException->getMessage()
            );
        }

        $bMailerConfigured = trim($this->sMailerDsn) !== '' || trim((string)ini_get('sendmail_path')) !== '';
        $this->addCheck(
            $aChecks,
            'Email transport',
            $bMailerConfigured,
            $bMailerConfigured
                ? 'A mail transport is configured; delivery still requires an external test.'
                : 'Configure PH7_MAILER_DSN or a working server sendmail transport.'
        );

        $iOldestCacheTime = $this->oldestCacheModificationTime();
        $bHasStaleCache = $iOldestCacheTime > 0 && ($this->iNow - $iOldestCacheTime) > self::STALE_CACHE_SECONDS;
        $aChecks[] = $this->check(
            'Cache age',
            $bHasStaleCache ? 'warning' : 'pass',
            $bHasStaleCache
                ? 'Cache files older than 30 days exist; review and clear caches after deployment.'
                : 'No cache file older than 30 days was found.'
        );

        return $aChecks;
    }

    public static function hasFailures(array $aChecks): bool
    {
        foreach ($aChecks as $aCheck) {
            if (($aCheck['status'] ?? '') === 'fail') {
                return true;
            }
        }

        return false;
    }

    private function probeDatabase(array $aConfig): array
    {
        $aDatabase = $aConfig['database'] ?? [];
        $sPrefix = (string)($aDatabase['prefix'] ?? '');
        if (preg_match('/^[A-Za-z0-9_]+$/D', $sPrefix) !== 1) {
            throw new \RuntimeException('The configured table prefix is invalid.');
        }

        $oDatabase = new \PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                (string)($aDatabase['hostname'] ?? ''),
                (int)($aDatabase['port'] ?? 3306),
                (string)($aDatabase['name'] ?? ''),
                (string)($aDatabase['charset'] ?? 'utf8mb4')
            ),
            (string)($aDatabase['username'] ?? ''),
            (string)($aDatabase['password'] ?? ''),
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );

        $sVersion = (string)$oDatabase->query('SELECT VERSION()')->fetchColumn();
        $sSchema = (string)$oDatabase->query(
            sprintf("SELECT version FROM %smodules WHERE vendorName = 'pH7Builder' LIMIT 1", $sPrefix)
        )->fetchColumn();
        $sCronSecret = (string)$oDatabase->query(
            sprintf("SELECT settingValue FROM %ssettings WHERE settingName = 'cronSecurityHash' LIMIT 1", $sPrefix)
        )->fetchColumn();

        return ['version' => $sVersion, 'schema' => $sSchema, 'cron_secret' => $sCronSecret];
    }

    private function runtimePaths(): array
    {
        return [
            'public data' => $this->path('data'),
            'cache' => $this->path('_protected/data/cache'),
            'logs' => $this->path('_protected/data/log'),
            'temporary data' => $this->path('_protected/data/tmp'),
            'backups' => $this->path('_protected/data/backup')
        ];
    }

    private function oldestCacheModificationTime(): int
    {
        $sCachePath = $this->path('_protected/data/cache');
        if (!is_dir($sCachePath)) {
            return 0;
        }

        $iOldest = 0;
        $oIterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sCachePath, \FilesystemIterator::SKIP_DOTS)
        );
        foreach ($oIterator as $oFile) {
            if ($oFile->isFile() && ($iOldest === 0 || $oFile->getMTime() < $iOldest)) {
                $iOldest = $oFile->getMTime();
            }
        }

        return $iOldest;
    }

    private function normaliseVersion(string $sVersion): string
    {
        return preg_match('/\d+(?:\.\d+){1,2}/', $sVersion, $aMatches) === 1 ? $aMatches[0] : '';
    }

    private function addCheck(array &$aChecks, string $sName, bool $bPassed, string $sDetail): void
    {
        $aChecks[] = $this->check($sName, $bPassed ? 'pass' : 'fail', $sDetail);
    }

    private function check(string $sName, string $sStatus, string $sDetail): array
    {
        return ['name' => $sName, 'status' => $sStatus, 'detail' => $sDetail];
    }

    private function path(string $sRelativePath): string
    {
        return rtrim($this->sProjectRoot, '/\\') . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $sRelativePath);
    }
}
