<?php
/**
 * @title            Autoloader Class
 * @desc             Loading Framework Class of CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Loader
 * @version          1.2
 */

namespace PH7\Framework\Loader;
defined('PH7') or exit('Restricted access');

/**
 * We include the Singleton trait before use, because at this stage the class can not load the trait automatically.
 */
require_once PH7_PATH_FRAMEWORK . 'Pattern/Base.trait.php';
require_once PH7_PATH_FRAMEWORK . 'Pattern/Singleton.trait.php';

final class Autoloader
{

    /**
     * We use this class with the singleton pattern.
     */
    use \PH7\Framework\Pattern\Singleton;

    /**
     * We do not put a "__construct" and "__clone" "private" because it is already included in the class \PH7\Framework\Pattern\Base that is included in the \PH7\Framework\Pattern\Singleton class.
     */

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
                $sFile =  PH7_PATH_FRAMEWORK . $sClass . '.interface.php';
            break;


            /***** To include third-party library that does not have the same naming convention that our CMS *****/

            // To include PFBC library
            case is_file(PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/' . $sClass . '.class.php'):
                $sFile = PH7_PATH_FRAMEWORK . 'Layout/Form/Engine/' . $sClass . '.class.php';
            break;

            // To include SwiftMailer library
            case 0 === strpos($sClass, 'Swift'):
                $sFile = PH7_PATH_FRAMEWORK . 'Mail/Engine/Swift/swift_required.php';
            break;

            default:
                return; // Stop it
        }

        require_once $sFile;
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
    }

}
