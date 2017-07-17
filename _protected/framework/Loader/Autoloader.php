<?php
/**
 * @title            Autoloader Class
 * @desc             Loading Framework Class of pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Loader
 * @version          1.9
 */

namespace PH7\Framework\Loader;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\File;
use PH7\Framework\Pattern\Singleton;

/**
 * We include the Singleton trait before use, because at this stage the class can not load the trait automatically.
 */
require_once PH7_PATH_FRAMEWORK . 'Pattern/Statik.trait.php';
require_once PH7_PATH_FRAMEWORK . 'Pattern/Singleton.trait.php';

final class Autoloader
{
    const MIN_VALID_SIZE_FILE = 1000;

    /**
     * Make the class singleton by importing the appropriate trait.
     */
    use Singleton;

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
        spl_autoload_register(array(__CLASS__, 'loadClass'));

        // Include Composer libraries (GeoIp2, Swift, Stripe, ...)
        $this->loadComposerLoader();
    }

    /**
     * Autoload Classes.
     *
     * @param string $sClass
     *
     * @return void
     */
    private function loadClass($sClass)
    {
        $sClass = $this->clean($sClass);

        switch (true) {
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


            /***** To include third-party libraries that does not have the same naming convention than pH7CMS *****/

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
     * Get the filename of the file storage server.
     *
     * @param string $sFileNamePath A pH7Framework filename path.
     *
     * @return string The filename.
     */
    private function getServerFileName($sFileNamePath)
    {
        return md5(strtolower(str_replace(array('/', '.class', '.php'), '', $sFileNamePath))) . '.dwld';
    }

    /**
     * For all classes, hack to remove the namespace, slash and backslash.
     *
     * @param string The class name to clean.
     *
     * @return string The class cleaned.
     */
    private function clean($sClass)
    {
        return str_replace(array('PH7\Framework', '\\', '//'), array('/', '/', ''), $sClass);
    }

    /**
     * @param $sFullPathFile
     * @param File $oFile
     *
     * @return bool
     */
    private function isFileTooSmall($sFullPathFile, File $oFile)
    {
        return $oFile->size($sFullPathFile) < self::MIN_VALID_SIZE_FILE;
    }
}
