<?php
/**
 * This script checks the server requirements for pH7Builder software.
 *
 * It was written to be standalone and can be used in different projects.
 * If you want to use it in your project, please keep the license and the developer details below in order to have the right to distribute it.
 *
 * @package        Install
 * @file           requirements
 * @author         Pierre-Henry Soria
 * @email          <hello@ph7builder.com>
 * @copyright      (c) 2011-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license        MIT (https://opensource.org/licenses/MIT)
 * @language       (PHP) and (HTML5 + CSS)
 * @since          2011/10/25
 * @version        Last revision: 2026/08/08
 */

defined('PH7') or exit('Restricted access');

define('EXTENSION_KEY', 'extension');
define('CLASS_KEY', 'class');
define('FUNCTION_KEY', 'function');
define('DIRECTIVE_KEY', 'directive');

$aErrors = array();

if (does_meet_minimum_required_php_version()) {
    $aErrors[] = 'Your current PHP version is ' . PHP_VERSION . '. pH7Builder requires PHP ' . PH7_REQUIRED_SERVER_VERSION . ' or newer.<br /> Please ask your Web host to upgrade PHP to ' . PH7_REQUIRED_SERVER_VERSION . ' or newer.';
}

$aRequirementsNeeded = array(
    EXTENSION_KEY => array(
        'pdo_mysql' => 'PDO',
        'zip' => 'Zip',
        'zlib' => 'Zlib',
        'fileinfo' => 'Fileinfo',
        'gd' => 'GD',
        'mbstring' => 'mbstring',
        'exif' => 'exif',
        'iconv' => 'iconv',
        'openssl' => 'OpenSSL',
        'simplexml' => 'SimpleXML',
        'xml' => 'XML',
        'xmlwriter' => 'XMLWriter'
    ),
    CLASS_KEY => array(
        'DOMDocument' => 'dom'
    ),
    FUNCTION_KEY => array(
        'exif_imagetype' => 'exif',
        'imagettftext' => 'GD FreeType Support',
        'imagecreatefromwebp' => 'GD WebP read support',
        'imagewebp' => 'GD WebP write support',
        'curl_init' => 'cURL',
        'password_hash' => 'password hashing',
        'random_bytes' => 'cryptographically secure random bytes'
    ),
    DIRECTIVE_KEY => array(
        'file_uploads'
    )
);

foreach ($aRequirementsNeeded as $sType => $aRequirements) {
    if ($sType === EXTENSION_KEY) {
        foreach ($aRequirements as $sExtension => $sExtensionName) {
            if (!extension_loaded($sExtension)) {
                $aErrors[] = 'Please install "' . $sExtensionName . '" PHP extension.';
            }
        }
    }

    if ($sType === CLASS_KEY) {
        foreach ($aRequirements as $sClass => $sClassName) {
            if (!class_exists($sClass)) {
                $aErrors[] = 'Please install "' . $sClassName . '" PHP extension.';
            }
        }
    }

    if ($sType === FUNCTION_KEY) {
        foreach ($aRequirements as $sFunction => $sFunctionName) {
            if (!function_exists($sFunction)) {
                $aErrors[] = 'Please install "' . $sFunctionName . '" PHP extension.';
            }
        }
    }

    if ($sType === DIRECTIVE_KEY) {
        foreach ($aRequirements as $sDirective) {
            // FILTER_VALIDATE_BOOLEAN filter returns TRUE for "1", "true", "on" and "yes", FALSE otherwise
            if (filter_var(ini_get($sDirective), FILTER_VALIDATE_BOOLEAN) === false) {
                $aErrors[] = $sDirective . ' PHP directive needs to be enabled.';
            }
        }
    }
}

$aWritablePaths = array(
    PH7_ROOT_PUBLIC => 'the pH7Builder root directory (needed temporarily to create _constants.php)'
);

foreach ($aWritablePaths as $sPath => $sDescription) {
    if (!is_dir($sPath) || !is_writable($sPath)) {
        $aErrors[] = 'The web-server user must be able to write ' . $sDescription . '. Grant owner/group write access; do not use world-writable permissions.';
    }
}

$aRuntimeDirectories = array(
    PH7_ROOT_INSTALL . 'data/caches' => 'the installer state and access-token directory',
    PH7_ROOT_INSTALL . 'data/caches/smarty_compile' => 'the installer template cache directory',
    PH7_ROOT_INSTALL . 'data/caches/smarty_cache' => 'the installer cache directory',
    PH7_ROOT_INSTALL . 'data/logs' => 'the installer log directory'
);

foreach ($aRuntimeDirectories as $sPath => $sDescription) {
    if ((!is_dir($sPath) && !@mkdir($sPath, 0775, true)) || !is_writable($sPath)) {
        $aErrors[] = 'The web-server user must be able to create and write ' . $sDescription . '. Grant owner/group write access; do not use world-writable permissions.';
    }
}

$iErrors = !empty($aErrors) ? count($aErrors) : 0;
if ($iErrors > 0) {
    display_html_header('Requirements - pH7Builder Installation');

    printf('<h3 class="error underline italic">You have %d error(s):</h3>', $iErrors);

    for ($iKey = 0; $iKey < $iErrors; $iKey++) {
        printf('<p class="error">❌ %d) %s</p>', $iKey + 1, $aErrors[$iKey]);
    }

    display_html_footer();

    exit(1);
}

function does_meet_minimum_required_php_version()
{
    return version_compare(PHP_VERSION, PH7_REQUIRED_SERVER_VERSION, '<');
}

function display_html_header($sPageTitle)
{
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>', $sPageTitle, '</title><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><style>body{background:#EFEFEF;color:#555;font:normal 10pt Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.error{color:red;font-size:13px}.success{color:green}.success,.error{font-weight:bold}.italic{font-style:italic}.underline{text-decoration:underline}</style></head><body><div class="center">';
}

function display_html_footer()
{
    echo '</div></body></html>';
}
