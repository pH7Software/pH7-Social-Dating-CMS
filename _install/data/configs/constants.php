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
// The installer replaces these values once. Runtime requests must never derive
// security-sensitive links from the request-controlled Host header.
$sUrlProtocol = '%url_protocol%';
$sDomain = '%domain%';

// Host-only cookies are the safe default. PH7_COOKIE_DOMAIN can explicitly opt
// into a validated parent domain when cross-subdomain cookies are required.
$sDomain_cookie = '';

// Determine the current file of the application
$sScriptName = is_string($_SERVER['SCRIPT_NAME'] ?? null) ? $_SERVER['SCRIPT_NAME'] : '/index.php';
$sPhp_self = str_replace('\\', '/', dirname($sScriptName));


########## CONSTANTS ##########

##### OTHER #####
define('PH7_DS', DIRECTORY_SEPARATOR);
define('PH7_PS', PATH_SEPARATOR);
define('PH7_SH', '/'); // SlasH
define('PH7_CANONICAL_AUTHORITY_PINNED', true);
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
