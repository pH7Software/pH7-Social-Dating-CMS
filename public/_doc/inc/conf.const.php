<?php
/**
 * @author      Pierre-Henry Soria
 * @email       pierrehs@hotmail.com
 * @link        http://github.com/pH-7/Nav-Doc-Script-V2
 * @copyright   (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license     CC-BY - http://creativecommons.org/licenses/by/3.0/
 */

namespace PH7\Doc;
defined('PH7') or exit('Restricted access');

#################### VARIABLES ####################


########## URL ##########

$sHttp = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
$sPhp_self = dirname(htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES));

#################### CONSTANTS ####################


########## OTHERS ##########

define('SELF', (substr($sPhp_self,-1) !== '/') ? $sPhp_self . '/' : $sPhp_self);
define('RELATIVE', SELF);
define('DEF_LANG', 'en');
define('TPL', 'base');

########## URL ##########

define('PROT_URL', $sHttp);
define('ROOT_URL', PROT_URL . $_SERVER['HTTP_HOST'] . SELF);
define('STATIC_URL', RELATIVE . 'static/');

########## PATH ##########

define('ROOT_PATH', dirname(__DIR__) . '/');
define('DATA_PATH', ROOT_PATH . 'data/');

########## INFORMATION ##########

define('SITE_NAME', 'pH');
define('SITE_SLOGAN', 'Documentation');
