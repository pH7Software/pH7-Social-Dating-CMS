<?php
/**
 * @title Front Controller Class
 *
 * This class is used to instantiate the Controller and the action with the MVC pattern, in short it is the heart of pH7CMS's software.
 * It can also retrieve the URL roads, initialize the languages​​, themes, database, etc.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Router
 * @version          1.0
 */

namespace PH7\Framework\Mvc\Router;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Translate\Lang,
PH7\Framework\Layout\LoadTemplate,
PH7\Framework\Mvc\Model\Engine\Db,
PH7\Framework\Registry\Registry,
PH7\Framework\Config\Config,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Url\Uri,
PH7\Framework\Mvc\Router\Uri as UriRoute;

/**
 * @class Singleton Class
 */
final class FrontController
{

    const INDEX_FILE = 'index.php';

    private $oConfig, $oRegistry, $oHttpRequest, $oUri, $aRequestParameter, $bIsRouterRewritten = false;

    use \PH7\Framework\Pattern\Singleton; // Import the Singleton trait

    /**
     * Routing controllers.
     *
     * @access private
     */
    private function __construct()
    {
        /** Objects are created for the functioning of the class. * */
        $this->oConfig = Config::getInstance();
        $this->oRegistry = Registry::getInstance();
        $this->oHttpRequest = new Http;
        $this->oUri = Uri::getInstance();

        $this->indexFileRouter();

        if ($this->oUri->fragment(0) === 'asset' && $this->oUri->fragment(1) === 'gzip')
        {
            // Loading and compress CSS and JavaScript files
            $this->gzipRouter();
            exit;
        }

        /**
         * @internal We initialize the database after the compression of static files (\PH7\Framework\Mvc\Router\FrontController::gzipRouter() method),
         * so we can always display static files even if there are problems with the database.
         */
        $this->_databaseInitialize();

        /**
         * @internal "_languageInitialize()" method must be declared before the rest of the code, because it initializes the main language constants for the rest of the code.
         */
        $this->_languageInitialize();

        $this->_assetsInitialize();

        $this->launchRewritingRouter();

        $this->launchNonRewritingRouters();
    }

    /**
     *  If the module action isn't rewriting, we launch the basic router.
     *
     * @access private
     */
    private function launchNonRewritingRouters()
    {
        if (!$this->bIsRouterRewritten)
        {
            if ($this->oUri->fragment(0) === 'm')
                $this->simpleModuleRouter();
            else
                $this->simpleRouter();
        }
    }

    /**
     *  Router for the modules that are rewriting through the custom XML route file.
     *
     * @access private
     */
    private function launchRewritingRouter()
    {
        $oUrl = UriRoute::loadFile(new \DomDocument);
        foreach ($oUrl->getElementsByTagName('route') as $oRoute)
        {
            if (preg_match('`^' . $oRoute->getAttribute('url') . '/?(?:\?[^/]+\=[^/]+)?$`', $this->oHttpRequest->requestUri(), $aMatches))
            {
                $this->setRewritingRouter();

                $sPathModule = $oRoute->getAttribute('path') . PH7_SH;

                // Get module
                $this->oRegistry->module = $oRoute->getAttribute('module');

                // Check if file exist
                if (!$this->oConfig->load(PH7_PATH_APP . $sPathModule . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
                {
                    $this->notFound('The <b>' . $this->oRegistry->module .
                            '</b> system module is not found.<br />File: <b>' . PH7_PATH_APP . $sPathModule . $this->oRegistry->module . PH7_DS .
                            '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_APP . $sPathModule . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');
                    // It reloads the config.ini file for the new module "error"
                    $this->oConfig->load(PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
                }

                /***** PATH THE MODULE *****/
                $this->oRegistry->path_module = PH7_PATH_APP . $sPathModule . $this->oRegistry->module . PH7_DS;

                /***** URL THE MODULE *****/
                $this->oRegistry->url_module = PH7_URL_ROOT . $this->oRegistry->module . PH7_SH;

                /***** PATH THE TEMPLATE *****/
                $this->oRegistry->path_themes_module = PH7_PATH_ROOT . PH7_LAYOUT . $sPathModule . $this->oRegistry->module . PH7_DS . PH7_TPL;

                /***** URL THE TEMPLATE *****/
                $this->oRegistry->url_themes_module = PH7_RELATIVE . PH7_LAYOUT . $sPathModule . $this->oRegistry->module . PH7_SH . PH7_TPL;

                // Get the default controller
                $this->oRegistry->controller = ucfirst($oRoute->getAttribute('controller')) . 'Controller';

                // Get the default action
                $this->oRegistry->action = $oRoute->getAttribute('action');
                if ($oRoute->hasAttribute('vars'))
                {
                    $aVars = explode(',', $oRoute->getAttribute('vars'));
                    $iOffset = count($aVars);

                    foreach ($aMatches as $sKey => $sMatch)
                    {
                        if ($sKey !== 0)
                        {
                            $this->oHttpRequest->setGet($aVars[$sKey-1], $sMatch);

                            /** Request Parameter for the Router Rewriting mode. * */
                            $this->aRequestParameter = $this->oUri->segments($this->oUri->totalFragment()-$iOffset);
                        }
                    }
                }
                break;
            }
        }
        unset($oUrl);
    }

    /**
     * Simple Router.
     *
     * @access private
     * @return void
     */
    private function simpleRouter()
    {
        if ($this->oUri->fragment(0) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(0)))
        {
            // Set system module
            $this->oRegistry->module = $this->oUri->fragment(0);
        }
        else
        {
            // Get system module
            $this->oRegistry->module = DbConfig::getSetting('defaultSysModule');
        }

        // Check if file exist
        if (!$this->oConfig->load(PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->notFound('The <b>' . $this->oRegistry->module . '</b> system module is not found.<br />File: <b>' . PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS .
                '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');

            // It reloads the config.ini file for the new module "error"
            $this->oConfig->load(PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
        }

        /***** PATH THE MODULE *****/
        $this->oRegistry->path_module = PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS;

        /***** URL THE MODULE *****/
        $this->oRegistry->url_module = PH7_URL_ROOT . $this->oRegistry->module . PH7_SH;

        /***** PATH THE TEMPLATE *****/
        $this->oRegistry->path_themes_module = PH7_PATH_TPL_SYS_MOD . PH7_DS . $this->oRegistry->module . PH7_DS . PH7_TPL;

        /***** URL THE TEMPLATE *****/
        $this->oRegistry->url_themes_module = PH7_URL_TPL_SYS_MOD . $this->oRegistry->module . PH7_SH . PH7_TPL;

        if ($this->oUri->fragment(1) === 'asset' && $this->oUri->fragment(2) === 'ajax')
        {
            // Loading files Asynchronous Ajax
            $this->ajaxRouter($this->oRegistry->path_module);
            exit;

        }
        elseif ($this->oUri->fragment(1) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(1)))
        {
            // Set the controller
            $this->oRegistry->controller = ucfirst($this->oUri->fragment(1)) . 'Controller';
        }
        else
        {
            // Get the default controller
            $this->oRegistry->controller = ucfirst($this->oConfig->values['module']['default_controller']) . 'Controller';
        }

        if ($this->oUri->fragment(2) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(2)))
        {
            // Set the action
            $this->oRegistry->action = $this->oUri->fragment(2);
        }
        else
        {
            // Get the default action
            $this->oRegistry->action = $this->oConfig->values['module']['default_action'];
        }

        /** Request Parameter for the Simple Router mode. **/
        $this->aRequestParameter = $this->oUri->segments(3);
    }

    /**
     * Simple Module Router.
     *
     * @access private
     * @return void
     */
    private function simpleModuleRouter()
    {
        if ($this->oUri->fragment(1) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(1)))
        {
            // Set module
            $this->oRegistry->module = $this->oUri->fragment(1);
        }

        // Check if file exist
        if (!$this->oConfig->load(PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE))
        {
            $this->notFound('The <b>' . $this->oRegistry->module . '</b> module is not found.<br />File: <b>' . PH7_PATH_MOD . $this->oRegistry->module . PH7_DS .
                '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');
            // It reloads the config.ini file for the new module "error"
            $this->oConfig->load(PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
        }

        /***** PATH THE MODULE *****/
        $this->oRegistry->path_module = PH7_PATH_MOD . $this->oRegistry->module . PH7_DS;
        /***** URL THE MODULE *****/
        $this->oRegistry->url_module = PH7_URL_ROOT . 'm/' . $this->oRegistry->module . PH7_SH;
        /***** PATH THE TEMPLATE *****/
        $this->oRegistry->path_themes_module = PH7_PATH_TPL_MOD . $this->oRegistry->module . PH7_DS . PH7_TPL;
        /***** URL THE TEMPLATE *****/
        $this->oRegistry->url_themes_module = PH7_URL_TPL_MOD . $this->oRegistry->module . PH7_SH . PH7_TPL;

        if ($this->oUri->fragment(2) === 'asset' && $this->oUri->fragment(3) === 'ajax')
        {
            // Loading files Asynchronous Ajax
            $this->ajaxRouter($this->oRegistry->path_module);
            exit;
        }
        elseif ($this->oUri->fragment(2) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(2)))
        {
            // Set the controller
            $this->oRegistry->controller = ucfirst($this->oUri->fragment(2)) . 'Controller';
        }
        else
        {
            // Get the default controller
            $this->oRegistry->controller = ucfirst($this->oConfig->values['module']['default_controller']) . 'Controller';
        }

        if ($this->oUri->fragment(3) && preg_match('#^[a-z0-9\.\-_]+$#i', $this->oUri->fragment(3)))
        {
            // Set the action
            $this->oRegistry->action = $this->oUri->fragment(3);
        }
        else
        {
            // Get the default action
            $this->oRegistry->action = $this->oConfig->values['module']['default_action'];
        }

        /** Request Parameter for the Simple Module Router mode. **/
        $this->aRequestParameter = $this->oUri->segments(4);
    }

    /**
     * If the action is rewriting by the XML route file, set the correct router to be used.
     *
     * @access private
     * @return void
     */
    private function setRewritingRouter()
    {
        $this->bIsRouterRewritten = true;
    }

    /**
     * @access public
     * @return void
     */
    public function _databaseInitialize()
    {
        $aDriverOptions[\PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->oConfig->values['database']['charset'];

        Db::getInstance(
            /* DSN */
            $this->oConfig->values['database']['type'] . ':host=' . $this->oConfig->values['database']['hostname'] . ';port=' . $this->oConfig->values['database']['port'] . ';dbname=' . $this->oConfig->values['database']['name'],
            /* Username */
            $this->oConfig->values['database']['username'],
            /* Password */
            $this->oConfig->values['database']['password'],
            /* Driver */
            $aDriverOptions,
            /* Prefix */
            $this->oConfig->values['database']['prefix']
        );
    }

    /**
     * Removing the sensitive database information in the config object.
     *
     * @access public
     * @return void
     */
    public function _removeDatabaseInfo()
    {
        unset($this->oConfig->values['database']);
    }

    /**
     * Internationalization with Gettext.
     *
     * @access public
     * @return void
     */
    public function _languageInitialize()
    {
        if (!defined('PH7_PREF_LANG'))
            define('PH7_PREF_LANG', DbConfig::getSetting('defaultLanguage'));

        if (!defined('PH7_LANG_NAME')) {
            // Set the default language of the site and load the default language path
            define('PH7_LANG_NAME', (new Lang)->setDefaultLang(PH7_PREF_LANG)->init()->load('global', PH7_PATH_APP_LANG)->getLang());
        }

        /*** Get the ISO language code (the two first letters) ***/
        define('PH7_DEFAULT_LANG_CODE', substr(PH7_DEFAULT_LANG, 0, 2));
        define('PH7_LANG_CODE', substr(PH7_LANG_NAME, 0, 2));

        /*** Set locale environment variables for gettext ***/
        putenv('LC_ALL=' . PH7_LANG_NAME);
        setlocale(LC_ALL, PH7_LANG_NAME);
    }

    /**
     * @access public
     * @return void
     */
    public function _templateInitialize()
    {
        /***** Start Loading Views and Templates *****/

        $oLoadTpl = (new LoadTemplate)->setDefaultTpl(DbConfig::getSetting('defaultTemplate'));
        $oLoadTpl->tpl();
        $oLoadTpl->modTpl();
        $oLoadTpl->mailTpl();
        define('PH7_TPL_NAME', $oLoadTpl->getTpl());
        define('PH7_TPL_MOD_NAME', $oLoadTpl->getModTpl());
        define('PH7_TPL_MAIL_NAME', $oLoadTpl->getMailTpl());
        unset($oLoadTpl);
    }

    /**
     * @access public
     * @return void
     */
    public function _pathInitialize()
    {
        $this->oRegistry->action = strtolower($this->oRegistry->action);

        /***** SHORTCUTS PATH FOR MODULES *****/
        $this->oRegistry->path_module_controllers = $this->oRegistry->path_module . PH7_CTRL;
        $this->oRegistry->path_module_models = $this->oRegistry->path_module . PH7_MODELS;
        $this->oRegistry->path_module_views = $this->oRegistry->path_module . PH7_VIEWS;
        $this->oRegistry->path_module_forms = $this->oRegistry->path_module . PH7_FORMS;
        $this->oRegistry->path_module_inc = $this->oRegistry->path_module . PH7_INC;
        $this->oRegistry->path_module_config = $this->oRegistry->path_module . PH7_CONFIG;
        $this->oRegistry->path_module_lang = $this->oRegistry->path_module . PH7_LANG;
    }

    /**
     * Initialize the resources of the assets folders.
     *
     * @access public
     * @return void
     */
    public function _assetsInitialize()
    {
        if ($this->oUri->fragment(0) === 'asset')
        {
            switch ($this->oUri->fragment(1))
            {
                case 'ajax':
                    // Loading Asynchronous Ajax files
                    $this->ajaxRouter();
                break;

                case 'file':
                    // Loading files
                    $this->fileRouter();
                break;

                case 'cron':
                    // Loading Cron Jobs files
                    $this->cronRouter();
                break;

                case 'css':
                    // Loading Style sheet files
                    $this->cssRouter();
                break;

                case 'js':
                    // Loading JavaScript files
                    $this->jsRouter();
                break;

                default:
                    $this->notFound('Not found Asset file!', 1);
            }
            exit;
        }
    }

    /**
     * @access private
     * @return void
     */
    private function gzipRouter()
    {
        (new \PH7\Framework\Layout\Gzip\Gzip)->run();
    }

    /**
     * @access private
     * @return void
     */
    private function ajaxRouter($sMod = null)
    {
        \PH7\Framework\File\Import::pH7FwkClass('Ajax.Ajax');

        // Option for Content Type
        if ($this->oHttpRequest->getExists('option'))
        {
            if ($this->oHttpRequest->get('option') == 'plain')
                header('Content-Type: text/plain; charset=utf-8');
        }

        if (!empty($sMod))
        {
            // For module only!

            $this->_pathInitialize();

            $sFolder = ($this->oUri->fragment(4) && preg_match('#^[\w]+$#', $this->oUri->fragment(4))) ? PH7_DS . $this->oUri->fragment(4) : '';
            if (is_file($sMod . 'assets/ajax/' . $this->oUri->fragment(3) . $sFolder . 'Ajax.php'))
            {
                include_once $sMod . 'assets/ajax/' . $this->oUri->fragment(3) . $sFolder . 'Ajax.php';
            }
            else
            {
                $this->notFound('Error while loading the library of module ajax<br />File: ' . $sMod . 'assets' . PH7_DS . 'ajax' . PH7_DS . $this->oUri->fragment(3) . $sFolder . 'Ajax.php does not exist', 1);
            }
        }
        else
        {
            // For all scripts of the pH7 DatingCms
            $sFolder = ($this->oUri->fragment(3) && preg_match('#^[\w]+$#', $this->oUri->fragment(3))) ? PH7_DS . $this->oUri->fragment(3) : '';

            if (is_file(PH7_PATH_SYS . 'core/assets/ajax/' . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php'))
            {
                include_once PH7_PATH_SYS . 'core/assets/ajax/' . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php';
            }
            else
            {
                $this->notFound('Error while loading the library of ajax<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'ajax' . PH7_DS . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php does not exist', 1);
            }
        }
    }

    /**
     * @access private
     * @return void
     */
    private function fileRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/file/' . $this->oUri->fragment(2) . 'CoreFile.php'))
            include_once PH7_PATH_SYS . 'core/assets/file/' . $this->oUri->fragment(2) . 'CoreFile.php';
        else
            $this->notFound('Error while loading the file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'file' . PH7_DS . $this->oUri->fragment(2) . 'CoreFile.php does not exist', 1);
    }

    /**
     * @access private
     * @return void
     */
    private function cronRouter()
    {
        if (strcmp($this->oHttpRequest->get('secret_word'), DbConfig::getSetting('cronSecurityHash')) === 0)
        {
            if (is_file(PH7_PATH_SYS . 'core/assets/cron/' . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php'))
                require PH7_PATH_SYS . 'core/assets/cron/' . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php';
            else
                $this->notFound('Error while loading the Cron Jobs file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'cron' . PH7_DS . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php does not exist', 1);
        }
        else
        {
            \PH7\Framework\Http\Http::setHeadersByCode(403);
            exit('Secret word is invalid for the cron hash!');
        }
    }

    /**
     * @access private
     * @return void
     */
    private function cssRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/css/' . $this->oUri->fragment(2) . 'CoreCss.php'))
        {
            header('Content-Type: text/css');
            include_once PH7_PATH_SYS . 'core/assets/css/' . $this->oUri->fragment(2) . 'CoreCss.php';
        }
        else
        {
            $this->notFound('Error while loading the Javascript file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'css' . PH7_DS . $this->oUri->fragment(2) . 'CoreCss.php does not exist', 1);
        }
    }

    /**
     * @access private
     * @return void
     */
    private function jsRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/js/' . $this->oUri->fragment(2) . 'CoreJs.php'))
        {
            header('Content-Type: text/javascript');
            include_once PH7_PATH_SYS . 'core/assets/js/' . $this->oUri->fragment(2) . 'CoreJs.php';
        }
        else
        {
            $this->notFound('Error while loading the Javascript file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'js' . PH7_DS . $this->oUri->fragment(2) . 'CoreJs.php does not exist', 1);
        }
    }

    /**
     * Run Router!
     *
     * @access public
     * @return void
     */
    public function runRouter()
    {
        $this->_pathInitialize();

        /***** FOR FILE CONFIG .INI OF MODULE *****/
        $this->oConfig->load($this->oRegistry->path_module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        // PH7_DEFAULT_TPL_MOD constant has to be defined before calling "_templateInitialize()"
        define('PH7_DEFAULT_TPL_MOD', $this->oConfig->values['module']['default_theme']);

        $this->_templateInitialize();

        if (is_file($this->oRegistry->path_module_controllers . $this->oRegistry->controller . '.php'))
        {
            // For additional options modules
            if (is_file($this->oRegistry->path_module . 'Bootstrap.php'))
                require_once $this->oRegistry->path_module . 'Bootstrap.php'; // Include Bootstrap Module if there exists

            $sController = 'PH7\\' . $this->oRegistry->controller;
            if (class_exists($sController) && (new \ReflectionClass($sController))->hasMethod($this->oRegistry->action))
            {
                $oMvc = new \ReflectionMethod($sController, $this->oRegistry->action);
                if ($oMvc->isPublic())
                {
                    $oCtrl = new $sController;

                    // And finally we perform the controller's action
                    $oMvc->invokeArgs($oCtrl, $this->getRequestParameter());

                    // Destruct the object to minimize CPU resources
                    unset($oCtrl);
                }
                else
                {
                    $this->notFound('The <b>' . $this->oRegistry->action . '</b> method is not public!', 1);
                }
            }
            else
            {
                $this->notFound('The <b>' . $this->oRegistry->action . '</b> method of the <b>' . $this->oRegistry->controller . '</b> controller does not exist.', 1);
            }
        }
        else
        {
            $this->notFound('The <b>' . $this->oRegistry->controller . '</b> controller of the <b>' . $this->oRegistry->module .
                '</b> module is not found.<br />File: <b>' . $this->oRegistry->path_module . '</b>', 1);
        }
    }

    /**
     * Get the Request Parameters.
     *
     * @access private
     * @return array The Request Parameters if it exists, otherwise an empty array.
     */
    private function getRequestParameter()
    {
        $aRequest = array();

        if (count($this->aRequestParameter) > 0)
        {
            foreach ($this->aRequestParameter as $sVal)
            {
                $sVal = trim($this->secureRequestParameter($sVal));

                if ($sVal !== '')
                {
                    // Clean the slug URL
                    $sVal = $this->cleanSlugUrl($sVal);

                    $aRequest[] = $sVal;
                }
            }
        }
        $this->clearRequestParameter();

        return $aRequest;
    }

    /**
     * Clean the Slug Url.
     *
     * @access private
     * @param string $sVal The request action name to clean.
     * @return string
     */
    private function cleanSlugUrl($sVal)
    {
        return preg_replace('#&[^/]+\=[^/]+$#', '', $sVal);
    }

    /**
     * Secures the Request Parameter.
     *
     * @access private
     * @param string $sVar
     * @return string
     */
    private function secureRequestParameter($sVar)
    {
        $sVar = escape($sVar, true);

        // Convert programatic characters to entities and return
        return str_replace(array(
            '$',
            '(',
            ')',
            '%28',
            '%29'), // Bad
            array(
            '&#36;',
            '&#40;',
            '&#41;',
            '&#40;',
            '&#41;'), // Good
            $sVar);
    }

    /**
     * Remove the Request Parameter variable.
     *
     * @access private
     * @return void
     */
    private function clearRequestParameter()
    {
        unset($this->aRequestParameter);
    }

    /**
     * We display an error page if it on the index file to indicate no file extension in order to avoid utilization of a security vulnerability  in the language.
     * Otherwise, if the URL rewrite extension is not enabled, we redirect the page to index.php file (then [URL]/index.php/[REQUEST]/ ).
     *
     * @access private
     * @see \PH7\Framework\Mvc\Router\FrontController\notFound()
     * @return void
     */
    private function indexFileRouter()
    {
        // The following code will be useless when pH7CMS will be able to work without mod_rewrite \\
        if ($this->oHttpRequest->currentUrl() === PH7_URL_ROOT . static::INDEX_FILE)
            $this->notFound('If we\'re in production mode, we display an error page if it on the index file to indicate no file extension in order to avoid utilization of a security vulnerability  in the language.');

        /*

        // The following code will be useful when pH7CMS will be able to work without mod_rewrite \\
        if (!\PH7\Framework\Server\Server::isRewriteMod() && false === strpos($this->oHttpRequest->currentUrl(), static::INDEX_FILE))
        {
            \PH7\Framework\Url\Header::redirect(PH7_URL_ROOT . static::INDEX_FILE);
        }
        elseif (\PH7\Framework\Server\Server::isRewriteMod() && false !== strpos($this->oHttpRequest->currentUrl(), static::INDEX_FILE))
        {
            $this->notFound('If we are in production mode, we display an error page if it is on the index.php file to indicate no file extension in order to avoid utilization of a security vulnerability in the PHP programming language.');
        }

        */
    }

    /**
     * This method has two different behavior compared to the mode site.
     * 1. In production mode: Displays the page not found using the system module "error".
     * 2. In development mode: It throws an Exception with displaying an explanatory message that indicates why this page was not found.
     *
     * @access private
     * @param string $sMsg
     * @param string $iRedirect 1 = redirect
     * @return void
     * @throws \PH7\Framework\Error\CException\PH7Exception If the site is in development mode, displays an explanatory message that indicates why this page was not found.
     */
    private function notFound($sMsg = null, $iRedirect = null)
    {
        if (isDebug() && !empty($sMsg))
        {
            throw new \PH7\Framework\Error\CException\PH7Exception($sMsg);
        }
        else
        {
            if (empty($iRedirect))
                $this->oRegistry->module = 'error';
            else
                \PH7\Framework\Url\Header::redirect(UriRoute::get('error', 'http', 'index'));
        }
    }

    public function __destruct()
    {
        unset($this->oConfig, $this->oRegistry, $this->oHttpRequest, $this->oUri, $this->bIsRouterRewritten);
    }

}
