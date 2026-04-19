<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link             https://ph7builder.com
 * @package          PH7
 */

namespace PH7;

defined('PH7') or exit(header('Location: ./'));

########## VARIABLES ##########

##### URL #####
// Check the SSL protocol compatibility
$sUrlProtocol = (
    (!empty($_SERVER['HTTPS']) && in_array(strtolower((string)$_SERVER['HTTPS']), ['on', '1'], true)) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos((string)$_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0) ||
    (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') ||
    (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') ||
    ($_SERVER['SERVER_PORT'] ?? '') === '443'
) ? 'https://' : 'http://';

// Determine the domain name, with the port if necessary
$sServerName = ($_SERVER['SERVER_NAME'] ?? '') !== ($_SERVER['HTTP_HOST'] ?? '') ? ($_SERVER['HTTP_HOST'] ?? '') : ($_SERVER['SERVER_NAME'] ?? '');
$sDomain = (($_SERVER['SERVER_PORT'] ?? '') !== '80' && ($_SERVER['SERVER_PORT'] ?? '') !== '443' && strpos($sServerName, ':') === false) ? $sServerName . ':' . ($_SERVER['SERVER_PORT'] ?? '') : $sServerName;

// Get the domain that the cookie and cookie session is available (Set-Cookie: domain=your_site_name.com)
$sCookieHost = strtolower(trim((string)$sServerName));
if (strpos($sCookieHost, '[') === 0) {
    $iClosingBracketPos = strpos($sCookieHost, ']');
    if ($iClosingBracketPos !== false) {
        $sCookieHost = substr($sCookieHost, 0, $iClosingBracketPos + 1);
    }
} elseif (substr_count($sCookieHost, ':') === 1) {
    $sCookieHost = (string)preg_replace('/:\d+$/', '', $sCookieHost);
}
$sCookieHost = trim($sCookieHost, '[]');
$bIsCookieHostIp = filter_var($sCookieHost, FILTER_VALIDATE_IP) !== false;
$bIsCookieHostLocal = $sCookieHost === 'localhost' || $sCookieHost === '';
$sDomain_cookie = (!$bIsCookieHostIp && !$bIsCookieHostLocal) ? '.' . preg_replace('/^www\./', '', $sCookieHost) : '';

// Determine the current file of the application
$sPhp_self = str_replace('\\', '', dirname(htmlspecialchars($_SERVER['PHP_SELF'] ?? '', ENT_QUOTES))); // Remove backslashes for Windows compatibility


########## CONSTANTS ##########

##### OTHER #####
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);
define('PH7_SH', '/'); // SlasH
define('PH7_SELF', (substr($sPhp_self, -1) !== PH7_SH) ? $sPhp_self . PH7_SH : $sPhp_self);
define('PH7_RELATIVE', PH7_SELF);

##### PATH #####
define('PH7_PATH_ROOT', __DIR__ . PH7_DS);
$sProtectedPath = '%path_protected%';
if (!is_dir($sProtectedPath)) {
    $aProtectedCandidates = [
        PH7_PATH_ROOT . '_protected' . PH7_DS,
        dirname(PH7_PATH_ROOT) . PH7_DS . '_protected' . PH7_DS
    ];

    foreach ($aProtectedCandidates as $sCandidatePath) {
        if (is_dir($sCandidatePath)) {
            $sProtectedPath = $sCandidatePath;
            break;
        }
    }
}

define('PH7_PATH_PROTECTED', $sProtectedPath);
define('PH7_PATH_APP', PH7_PATH_PROTECTED . 'app/');
define('PH7_PATH_FRAMEWORK', PH7_PATH_PROTECTED . 'framework/');
define('PH7_PATH_LIBRARY', PH7_PATH_PROTECTED . 'library/');

##### URL (PUBLIC) #####
define('PH7_URL_PROT', $sUrlProtocol);
define('PH7_DOMAIN', $sDomain); // URL domain
define('PH7_DOMAIN_COOKIE', $sDomain_cookie);
define('PH7_URL_ROOT', PH7_URL_PROT . PH7_DOMAIN . PH7_SELF);
