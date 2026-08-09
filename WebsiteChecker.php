<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link           https://ph7builder.com
 * @package        PH7 / ROOT
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit(header('Location: ./'));

use RuntimeException;

class WebsiteChecker
{
    private const REQUIRED_SERVER_VERSION = '8.2.0';
    private const REQUIRED_CONFIG_FILE_NAME = '_constants.php';
    private const INSTALL_FOLDER_NAME = '_install/';

    private const PHP_VERSION_ERROR_MESSAGE = 'ERROR: Your current PHP version is %s. pH7Builder requires PHP %s or newer.<br /> Please ask your Web host to upgrade PHP to %s or newer.';
    private const NO_CONFIG_FOUND_ERROR_MESSAGE = 'CONFIG FILE NOT FOUND! If you want to make a new installation, please re-upload _install/ folder and clear your database.';

    /**
     * @throws RuntimeException
     */
    public function checkPhpVersion(): void
    {
        if ($this->isIncompatiblePhpVersion()) {
            throw new RuntimeException(
                sprintf(
                    self::PHP_VERSION_ERROR_MESSAGE,
                    PHP_VERSION,
                    self::REQUIRED_SERVER_VERSION,
                    self::REQUIRED_SERVER_VERSION
                )
            );
        }
    }

    /**
     * Normalize legacy request-derived URL constants before _constants.php is loaded.
     *
     * This keeps upgraded installations safe when their generated constants file
     * predates the canonical-host template introduced in 18.6.0.
     *
     * @throws RuntimeException
     */
    public function normalizeRequestAuthority(): void
    {
        $mConfiguredHost = getenv('PH7_CANONICAL_HOST');
        $bHasConfiguredHost = is_string($mConfiguredHost) && trim($mConfiguredHost) !== '';
        $sCanonicalHost = $bHasConfiguredHost
            ? trim($mConfiguredHost)
            : (string)($_SERVER['SERVER_NAME'] ?? '');

        if (!$this->isValidCanonicalHost($sCanonicalHost)) {
            if ($bHasConfiguredHost) {
                throw new RuntimeException(
                    'Configuration error: PH7_CANONICAL_HOST must contain only a hostname or IP address and an optional valid port.'
                );
            }

            throw new RuntimeException(
                'Configuration error: the web server has no valid canonical ServerName. Configure the virtual host or set PH7_CANONICAL_HOST.'
            );
        } else {
            $aAuthority = parse_url('http://' . $sCanonicalHost);
            $iAuthorityPort = is_array($aAuthority) && isset($aAuthority['port'])
                ? (int)$aAuthority['port']
                : null;

            if ($bHasConfiguredHost) {
                $_SERVER['SERVER_PORT'] = (string)($iAuthorityPort ?? ($this->isEffectiveHttpsRequest() ? 443 : 80));
            } else {
                $iServerPort = filter_var(
                    $_SERVER['SERVER_PORT'] ?? null,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => 1, 'max_range' => 65535]]
                );
                if ($iAuthorityPort === null && is_int($iServerPort) && !in_array($iServerPort, [80, 443], true)) {
                    $sCanonicalHost .= ':' . $iServerPort;
                }
            }

            $_SERVER['HTTP_HOST'] = $sCanonicalHost;
        }

        if (getenv('PH7_TRUST_PROXY_HEADERS') !== '1') {
            unset($_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['HTTP_X_FORWARDED_SSL']);
        }
    }

    /**
     * Clear redirection cache since some folks get it cached.
     *
     * @return void
     */
    public function clearBrowserCache(): void
    {
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }

    public function moveToInstaller(): void
    {
        header('Location: ' . self::INSTALL_FOLDER_NAME);
    }

    public function doesConfigFileExist(): bool
    {
        return is_file(__DIR__ . '/' . self::REQUIRED_CONFIG_FILE_NAME);
    }

    public function doesConfigPinCanonicalAuthority(): bool
    {
        $sConfigPath = __DIR__ . '/' . self::REQUIRED_CONFIG_FILE_NAME;
        $sConfig = is_readable($sConfigPath) ? file_get_contents($sConfigPath) : false;

        return is_string($sConfig) &&
            str_contains($sConfig, "define('PH7_CANONICAL_AUTHORITY_PINNED', true);");
    }

    public function getNoConfigFoundMessage(): string
    {
        return self::NO_CONFIG_FOUND_ERROR_MESSAGE;
    }

    public function doesInstallFolderExist(): bool
    {
        return is_dir(__DIR__ . '/' . self::INSTALL_FOLDER_NAME);
    }

    private function isIncompatiblePhpVersion(): bool
    {
        return version_compare(PHP_VERSION, self::REQUIRED_SERVER_VERSION, '<');
    }

    private function isValidCanonicalHost(string $sHost): bool
    {
        $aMatches = [];
        if (preg_match('/^(?:\[[0-9a-f:.]+\]|[a-z0-9.-]+)(?::([0-9]{1,5}))?$/iD', $sHost, $aMatches) !== 1) {
            return false;
        }

        return !isset($aMatches[1]) || ((int)$aMatches[1] >= 1 && (int)$aMatches[1] <= 65535);
    }

    private function isEffectiveHttpsRequest(): bool
    {
        $bTrustProxyHeaders = getenv('PH7_TRUST_PROXY_HEADERS') === '1';
        $sForwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]));

        return (!empty($_SERVER['HTTPS']) && in_array(strtolower((string)$_SERVER['HTTPS']), ['on', '1'], true)) ||
            ($bTrustProxyHeaders && $sForwardedProto === 'https') ||
            ($bTrustProxyHeaders && strtolower((string)($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on') ||
            (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ||
            ($_SERVER['SERVER_PORT'] ?? '') === '443';
    }
}
