<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

defined('PH7') or exit('Restricted access');

//---------- Variables ----------//

//----- URL -----//
// Trust reverse-proxy headers only when the operator explicitly enables them.
$bTrustProxyHeaders = getenv('PH7_TRUST_PROXY_HEADERS') === '1';
$sForwardedProto = strtolower(trim(explode(',', (string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''))[0]));

// Check the SSL protocol compatibility.
$sUrlProtocol = (
    (!empty($_SERVER['HTTPS']) && in_array(strtolower((string)$_SERVER['HTTPS']), ['on', '1'], true)) ||
    ($bTrustProxyHeaders && $sForwardedProto === 'https') ||
    ($bTrustProxyHeaders && strtolower((string)($_SERVER['HTTP_X_FORWARDED_SSL'] ?? '')) === 'on') ||
    (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ||
    ($_SERVER['SERVER_PORT'] ?? '') === '443'
) ? 'https://' : 'http://';

// HTTP_HOST is request-controlled. Prefer an operator-defined canonical host,
// then the web server's virtual-host name.
$mCanonicalHost = getenv('PH7_CANONICAL_HOST');
$bHasConfiguredCanonicalHost = is_string($mCanonicalHost) && trim($mCanonicalHost) !== '';
$sServerName = is_string($mCanonicalHost) && trim($mCanonicalHost) !== ''
    ? trim($mCanonicalHost)
    : (string)($_SERVER['SERVER_NAME'] ?? '');
$aServerNameMatches = [];
$bValidServerName = is_string($sServerName) && preg_match(
    '/^(?:\[[0-9a-f:.]+\]|[a-z0-9.-]+)(?::([0-9]{1,5}))?$/iD',
    $sServerName,
    $aServerNameMatches
) === 1;
if (!$bValidServerName || isset($aServerNameMatches[1]) &&
    ((int)$aServerNameMatches[1] < 1 || (int)$aServerNameMatches[1] > 65535)
) {
    http_response_code(500);
    exit($bHasConfiguredCanonicalHost
        ? 'Configuration error: PH7_CANONICAL_HOST must contain only a hostname or IP address and an optional valid port.'
        : 'Configuration error: the web server has no valid canonical ServerName. Configure the virtual host or set PH7_CANONICAL_HOST before installing.');
}
$iServerPort = filter_var(
    $_SERVER['SERVER_PORT'] ?? null,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 65535]]
);
$iServerPort = is_int($iServerPort) ? $iServerPort : ($sUrlProtocol === 'https://' ? 443 : 80);
$bHasExplicitPort = !empty($aServerNameMatches[1]);
$sDomain = !$bHasConfiguredCanonicalHost && !$bHasExplicitPort && !in_array($iServerPort, [80, 443], true)
    ? $sServerName . ':' . $iServerPort
    : $sServerName;

// Determine the current file of the application
$sScriptName = is_string($_SERVER['SCRIPT_NAME'] ?? null) ? $_SERVER['SCRIPT_NAME'] : '/_install/index.php';
$sPhp_self = str_replace('\\', '/', dirname($sScriptName));

//---------- Constants ----------//

//----- Other -----//
define('PH7_ADMIN_MOD', 'admin123');
define('PH7_REQUIRED_SERVER_VERSION', '8.2.0');
define('PH7_REQUIRED_SQL_VERSION', '8.0.0');
define('PH7_ENCODING', 'utf-8');
define('PH7_DEFAULT_TIMEZONE', 'America/Chicago');
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);

//----- URL -----//
define('PH7_URL_INSTALL', $sUrlProtocol . $sDomain . $sPhp_self . '/'); // INSTALL URL
define('PH7_URL_ROOT', dirname(PH7_URL_INSTALL) . '/'); // ROOT URL

//----- PATH -----//
define('PH7_ROOT_PUBLIC', dirname(__DIR__) . PH7_DS); // PUBLIC ROOT
define('PH7_ROOT_INSTALL', __DIR__ . PH7_DS); // ROOT INSTALL'
define('PH7_PATH_PUBLIC_DATA_SYS_MOD', PH7_ROOT_PUBLIC . 'data/system/modules/');
