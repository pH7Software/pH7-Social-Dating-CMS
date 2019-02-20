<?php
/**
 * @title            Autoloader Class
 * @desc             Loading classes to include additional.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class / Loader
 * @version          1.0
 */

namespace PH7\App\Includes\Classes\Loader;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Singleton;
use PH7\Framework\Registry\Registry;

final class Autoloader
{
    const PROJECT_NAMESPACE = 'PH7\\';

    /**
     * It's a singleton class, so include the singleton trait.
     */
    use Singleton;

    /**
     * We do not put a "__construct" and "__clone" "private" because it is already done in the \PH7\Framework\Pattern\Statik trait which is included in the Singleton trait.
     */


    /**
     * Init Autoload Class.
     *
     * @return void
     */
    public function init()
    {
        // Specify the extensions that may be loaded
        spl_autoload_extensions('.php');
        /** Register the loader methods **/
        spl_autoload_register([__CLASS__, 'loadController']);
        spl_autoload_register([__CLASS__, 'loadClass']);
        spl_autoload_register([__CLASS__, 'loadModel']);
        spl_autoload_register([__CLASS__, 'loadForm']);
    }

    /**
     * Autoload Controllers.
     *
     * @param string $sClass
     *
     * @return void
     */
    private function loadController($sClass)
    {
        $sClass = $this->removeNamespace($sClass);

        // For the Controllers of the modules
        if (is_file(Registry::getInstance()->path_module_controllers . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_controllers . $sClass . '.php';
        }
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
        $sClass = $this->removeNamespace($sClass);

        // For the global Classes of the pH7Framework
        if (is_file(PH7_PATH_APP . 'includes/classes/' . $sClass . '.php')) {
            require_once PH7_PATH_APP . 'includes/classes/' . $sClass . '.php';
        }

        // For the Core Classes
        if (is_file(PH7_PATH_SYS . 'core/classes/' . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/classes/' . $sClass . '.php';
        }

        // For the Classes of the modules
        if (is_file(Registry::getInstance()->path_module_inc . 'class/' . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_inc . 'class/' . $sClass . '.php';
        }

        // For the Core Designs Classes
        if (is_file(PH7_PATH_SYS . 'core/classes/design/' . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/classes/design/' . $sClass . '.php';
        }

        // For the Designs Classes of the modules
        if (is_file(Registry::getInstance()->path_module_inc . 'class/design/' . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_inc . 'class/design/' . $sClass . '.php';
        }
    }

    /**
     * Autoload Models.
     *
     * @param string $sClass
     *
     * @return void
     */
    private function loadModel($sClass)
    {
        $sClass = $this->removeNamespace($sClass);

        // For the Core Models
        if (is_file(PH7_PATH_SYS . 'core/' . PH7_MODELS . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/' . PH7_MODELS . $sClass . '.php';
        }

        // For the Models of the modules
        if (is_file(Registry::getInstance()->path_module_models . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_models . $sClass . '.php';
        }

        // For the Core Designs Models
        if (is_file(PH7_PATH_SYS . 'core/' . PH7_MODELS . 'design/' . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/' . PH7_MODELS . 'design/' . $sClass . '.php';
        }

        // For the Designs Models of the modules
        if (is_file(Registry::getInstance()->path_module_models . 'design/' . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_models . 'design/' . $sClass . '.php';
        }
    }

    /**
     * Autoload Forms.
     *
     * @param string $sClass
     *
     * @return void
     */
    private function loadForm($sClass)
    {
        $sClass = $this->removeNamespace($sClass);

        // For the Core Forms
        if (is_file(PH7_PATH_SYS . 'core/' . PH7_FORMS . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/' . PH7_FORMS . $sClass . '.php';
        }

        if (is_file(PH7_PATH_SYS . 'core/' . PH7_FORMS . 'processing/' . $sClass . '.php')) {
            require_once PH7_PATH_SYS . 'core/' . PH7_FORMS . 'processing/' . $sClass . '.php';
        }

        // For the Forms of the modules
        if (is_file(Registry::getInstance()->path_module_forms . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_forms . $sClass . '.php';
        }

        if (is_file(Registry::getInstance()->path_module_forms . 'processing/' . $sClass . '.php')) {
            require_once Registry::getInstance()->path_module_forms . 'processing/' . $sClass . '.php';
        }
    }

    /**
     * Hack to remove the 'PH7' namespace.
     *
     * @param string $sClass
     *
     * @return string
     */
    private function removeNamespace($sClass)
    {
        return str_replace(self::PROJECT_NAMESPACE, '', $sClass);
    }
}
