<?php
/**
 * @title            Autoloader Class
 * @desc             Loading Framework Class of pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Loader
 * @version          1.5
 */

namespace PH7\Framework\Loader;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\File\File,
PH7\Framework\Registry\Registry,
PH7\Framework\Date\Various as VDate;

/**
 * We include the Singleton trait before use, because at this stage the class can not load the trait automatically.
 */
require_once PH7_PATH_FRAMEWORK . 'Pattern/Statik.trait.php';
require_once PH7_PATH_FRAMEWORK . 'Pattern/Singleton.trait.php';

final class Autoloader
{
    const DOWNLOAD_URL = 'http://download.hizup.com/files/';

    /**
     * Make the class singleton by importing the appropriate trait.
     */
    use \PH7\Framework\Pattern\Singleton;

    /**
     * We do not put a "__construct" and "__clone" "private" because it is already done in the \PH7\Framework\Pattern\Statik trait which is included in the \PH7\Framework\Pattern\Singleton trait.
     */


    /**
     * Autoloader Class.
     *
     * @return void
     */
    public function init()
    {
        // Specify the extensions that may be loaded
        spl_autoload_extensions('.class.php, .interface.php, .trait.php');

        // Register the loader methods
        spl_autoload_register(array(__CLASS__, '_loadClass'));

        $this->_loadFile('Core/License.class.php');
        $this->_loadFile('Core/Kernel.class.php');

        // Include Composer libraries (GeoIp2, Swift, Stripe, ...)
        require_once PH7_PATH_PROTECTED . 'vendor/autoload.php';
    }

    /**
     * Display a message if the server isn't connected to the Internet.
     *
     * @return void Display an error message and exit the script if there is no Internet, otherwise doing nothing.
     */
    public function launchInternetCheck()
    {
        if (!\PH7\is_internet())
        {
            $sMsg = '<p class="warning">No Internet Connection</p>
            <p>Whoops! Your server has to be connected to the Internet in order to get your website working.</p>';

            echo \PH7\html_body('Enable your Internet connection', $sMsg);
            exit;
        }
    }

    /**
     * Autoload Classes.
     *
     * @param string $sClass
     * @return void
     */
    private function _loadClass($sClass)
    {
        $sClass = $this->_clean($sClass);

        switch (true)
        {
            /***** To include the libraries *****/

            // To include Classes
            case is_file(PH7_PATH_FRAMEWORK . $sClass . '.class.php'):
                $sFile = PH7_PATH_FRAMEWORK . $sClass . '.class.php';
            break;

            // To include Interfaces
            case is_file(PH7_PATH_FRAMEWORK . $sClass . '.interface.php'):
                $sFile = PH7_PATH_FRAMEWORK . $sClass . '.interface.php';
            break;

            // To include Traits
            case is_file(PH7_PATH_FRAMEWORK . $sClass . '.trait.php'):
                $sFile =  PH7_PATH_FRAMEWORK . $sClass . '.trait.php';
            break;


            /***** To include third-party library that does not have the same naming convention that our CMS *****/

            // Include PFBC (PHP Form Builder Class) library
            case is_file(PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/' . $sClass . '.class.php'):
                $sFile = PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/' . $sClass . '.class.php';
            break;

            default:
                return; // Stop it
        }

        require_once $sFile;
    }

    /**
     * Check and load the files if necessary.
     *
     * @param string $sFileNamePath A pH7Framework filename path.
     * @return void
     */
    private function _loadFile($sFileNamePath)
    {
        $oFile = new File;
        $sFullPath = PH7_PATH_FRAMEWORK . $sFileNamePath;
        $bIsExpiredFile = (VDate::setTime('-2 months') > $oFile->getModifTime($sFullPath));
        $bFileExists = $oFile->existFile($sFullPath);
        $bIsTooSmallFile = ($oFile->size($sFullPath) < 1000);

        if (!$bFileExists || $bIsTooSmallFile || $bIsExpiredFile)
        {
            /**
             * First off, check if the server is connected to the Internet in order to be able to download the remote files.
             */
            Registry::getInstance()->is_internet_needed = true;
            $this->launchInternetCheck();

            if ($bFileExists) // Delete the file if it already exists
                $oFile->deleteFile($sFullPath);

            $this->_downloadFile($sFileNamePath, $oFile);
        }
        else
        {
            Registry::getInstance()->is_internet_needed = false;
        }
        unset($oFile);
    }

    /**
     * Download Files protected by the license.
     *
     * @param string $sFileNamePath A pH7Framework filename path.
     * @param object \PH7\Framework\File\File $oFile
     * @return void
     */
    private function _downloadFile($sFileNamePath, File $oFile)
    {
        $rFile = $oFile->getUrlContents(self::DOWNLOAD_URL . $this->_getServerFileName($sFileNamePath));
        $oFile->putFile(PH7_PATH_FRAMEWORK . $sFileNamePath, $rFile);
    }

    /**
     * Get the filename of the file storage server.
     *
     * @param string $sFileNamePath A pH7Framework filename path.
     * @param object \PH7\Framework\File\File $oFile
     * @return string The filename.
     */
    private function _getServerFileName($sFileNamePath)
    {
        return md5(strtolower(str_replace(array('/', '.class', '.php'), '', $sFileNamePath))) . '.dwld';
    }

    /**
     * For all classes, hack to remove the namespace, slash and backslash.
     *
     * @param string The class name to clean.
     * @return string The class cleaned.
     */
    private function _clean($sClass)
    {
        return str_replace(array('PH7\Framework', '\\', '//'), array('/', '/', ''), $sClass);
    }
}
