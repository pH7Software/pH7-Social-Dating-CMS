<?php
/**
 * @title          Bootstrap
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link           http://ph7cms.com
 * @package        PH7 / App / Core
 * @version        1.4
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Server\Server,
PH7\Framework\Navigation\Browser,
PH7\Framework\Registry\Registry,
PH7\Framework\Mvc\Router\FrontController,
PH7\Framework\Config\Config,
PH7\Framework\File\Import,
PH7\Framework\Error\CException as Except;

/***** Begin Loading Files *****/

require 'configs/constants.php';
require 'includes/helpers/misc.php';

// Set a default timezone if it is not already configured
if (!ini_get('date.timezone'))
    ini_set('date.timezone', PH7_DEFAULT_TIMEZONE);

try
{
    /**
     * First off, check it the server is connected to the Internet.
     */
    if (!is_internet())
    {
        $sMsg = '<p class="warning">No Internet Connection</p>
        <p>Whoops! Your server has to be connect to the Internet in order to get your website working.</p>';

        echo html_body('Enable your Internet connection', $sMsg);
        exit;
    }

    // Loading Framework Classes
    require PH7_PATH_FRAMEWORK . 'Loader/Autoloader.php';
    Framework\Loader\Autoloader::getInstance()->init();

    /** Loading configuration files environments **/
    // For All environment
    Import::file(PH7_PATH_APP . 'configs/environment/all.env');
    // Specific to the current environment
    Import::file(PH7_PATH_APP . 'configs/environment/' . Config::getInstance()->values['application']['environment'] . '.env');

    // Loading Class ~/protected/app/includes/classes/*
    Import::pH7App('includes.classes.Loader.Autoloader');
    App\Includes\Classes\Loader\Autoloader::getInstance()->init();

    // Loading Debug class
    Import::pH7FwkClass('Error.Debug');

    // Loading String Class
    Import::pH7FwkClass('Str.Str');

    // We expect that this function is simply used // Import::pH7FwkClass('Structure.General');


    /*** End Loading Files ***/

    //** Temporary code. In the near future, pH7CMS will be usable without mod_rewrite
    if (!Server::isRewriteMod())
    {
        $sMsg = '<p class="warning"><a href="' . Framework\Core\Kernel::SOFTWARE_WEBSITE . '">pH7CMS</a> requires Apache "mod_rewrite".</p>
        <p>Please install it so that pH7CMS can works.<br /> Click <a href="http://ph7cms.com/doc/en/how-to-install-rewrite-module" target="_blank">here</a> if you want to get more information on how to install the rewrite module.<br /><br />
        After doing this, please <a href="' . PH7_URL_ROOT . '">retry</a>.</p>';

        echo html_body("Apache's mod_rewrite is required", $sMsg);
        exit;
    }  //*/

    // Enable client browser cache
    (new Browser)->cache();

    // Starting zlib-compressed output
    /*
       This "zlib output compression" compressthe pages.
       This allows you to save your bandwidth and faster download of your pages.
       WARNING: this function consumes CPU resources on your server.
       So you can if you want to remove this function.
     */
    //ini_set('zlib.output_compression', 2048);
    //ini_set('zlib.output_compression_level', 6);
    ob_start();

    new Server; // Start Server

    Registry::getInstance()->start_time = microtime(true);

    /**
     * Initialize the FrontController, we are asking the front controller to process the HTTP request
     */
    FrontController::getInstance()->runRouter();
}

# \PH7\Framework\Error\CException\UserException
catch (Except\UserException $oE)
{
    echo $oE->getMessage(); // Simple User Error with Exception
}

# \PH7\Framework\Error\CException\PH7Exception
catch (Except\PH7Exception $oE)
{
    Except\PH7Exception::launch($oE);
}

# \Exception and other...
catch (\Exception $oE)
{
    Except\PH7Exception::launch($oE);
}

/* Soon in pH7CMS 2.0 version (when it will accept only PHP 5.5 or higher)
finally
{
    if ('' !== session_id()) session_write_close();
    ob_end_flush();
    exit(0);
}*/

# Finally Block Emulator because PHP does not support finally block.
if ('' !== session_id()) session_write_close();
ob_end_flush();
exit(0);