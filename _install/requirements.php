<?php
/**
 * This script checks the server requirements for pH7CMS software.
 *
 * It was written in order to be standarlone and can be used in different projects.
 * If you want to use it in your project, please keep the license and the developer details below in order to have the right to distribute it.
 *
 * @package        Install
 * @file           requirements
 * @author         Pierre-Henry Soria
 * @email          <hello@ph7cms.com>
 * @copyright      (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        Lesser General Public License (LGPL) (http://www.gnu.org/copyleft/lesser.html)
 * @language       (PHP) and (HTML5 + CSS)
 * @since          2011/10/25
 * @version        Last revision: 2017/10/23
 */

defined('PH7') or exit('Restricted access');

define('EXTENSION_KEY', 'extension');
define('CLASS_KEY', 'class');
define('FUNCTION_KEY', 'function');
define('DIRECTIVE_KEY', 'directive');

$aErrors = array();

if (version_compare(PHP_VERSION, PH7_REQUIRED_SERVER_VERSION, '<')) {
    $aErrors[] = 'Your current PHP version is ' . PHP_VERSION . '. pH7CMS requires PHP ' . PH7_REQUIRED_SERVER_VERSION . ' or newer.<br /> Please ask your Web host to upgrade PHP to ' . PH7_REQUIRED_SERVER_VERSION . ' or newer.';
}

$aRequirementsNeeded = array(
    EXTENSION_KEY => array(
        'pdo_mysql' => 'PDO',
        'zip' => 'Zip',
        'zlib' => 'Zlib',
        'gd' => 'GD',
        'mbstring' => 'mbstring',
        'exif' => 'exif'
    ),
    CLASS_KEY => array(
        'DOMDocument' => 'dom'
    ),
    FUNCTION_KEY => array(
        'exif_imagetype' => 'exif',
        'curl_init' => 'cURL'
    ),
    DIRECTIVE_KEY => array(
        'file_uploads',
        'allow_url_fopen'
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

$iErrors = !empty($aErrors) ? count($aErrors) : 0;
if ($iErrors > 0) {
    display_html_header('Requirements - pH7CMS Installation');

    printf('<h3 class="error underline italic">You have %d error(s):</h3>', $iErrors);

    for ($iKey = 0; $iKey < $iErrors; $iKey++) {
        printf('<p class="error">%d) %s</p>', $iKey + 1, $aErrors[$iKey]);
    }

    display_html_footer();

    exit(1);
}


function display_html_header($sPageTitle)
{
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>', $sPageTitle, '</title><meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><style>body{background:#EFEFEF;color:#555;font:normal 10pt Arial,Helvetica,sans-serif;margin:0;padding:0}.center{margin-left:auto;margin-right:auto;text-align:center;width:80%}.error{color:red;font-size:13px}.success{color:green}.success,.error{font-weight:bold}.italic{font-style:italic}.underline{text-decoration:underline}</style></head><body><div class="center">';
}

function display_html_footer()
{
    echo '</div></body></html>';
}
