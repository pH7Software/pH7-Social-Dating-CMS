<?php
/**
 * @title Front Controller Class
 *
 * This class is used to instantiate the Controller and the action with the MVC pattern, in short it is the heart of pH7CMS's software.
 * It can also retrieve the URL roads, initialize the languages​​, themes, database, etc.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Router
 */

namespace PH7\Framework\Mvc\Router;

defined('PH7') or exit('Restricted access');

use DomDocument;
use DOMElement;
use PDO;
use PH7\Framework\Config\Config;
use PH7\Framework\Error\CException\PH7Exception;
use PH7\Framework\File\Import as FileImporter;
use PH7\Framework\Layout\Gzip\Gzip;
use PH7\Framework\Layout\LoadTemplate;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Db;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri as UriRoute;
use PH7\Framework\Pattern\Singleton;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;
use PH7\Framework\Url\Uri;
use ReflectionException;
use ReflectionMethod;
use Teapot\StatusCode;

/**
 * @class Singleton Class
 */
final class FrontController
{
    const PROJECT_NAMESPACE = 'PH7\\';
    const CONTROLLER_SUFFIX = 'Controller';
    const INDEX_FILE = 'index.php';

    const REDIRECT_ERROR_MOD = 1;

    const MAIN_GETTEXT_FILENAME = 'global';
    const SIMPLE_MOD_REQUEST_PARAM_NAME = 'm';
    const ASSET_REQUEST_PARAM_NAME = 'asset';
    const AJAX_REQUEST_PARAM_NAME = 'ajax';
    const GZIP_REQUEST_PARAM_NAME = 'gzip';

    const REGEX_MODULE_FORMAT = '#^[a-z0-9\.\-_]+$#i';
    const REGEX_CONTROLLER_FORMAT = '#^[a-z0-9_]+$#i';
    const REGEX_ACTION_FORMAT = '#^[a-z0-9_]+$#i';
    const REGEX_FOLDER_FORMAT = '#^[\w]+$#';
    const REGEX_URL_EXTRA_OPTIONS = '/?(?:\?[^/]+\=[^/]+)?';
    const REGEX_URL_PARAMS = '#&[^/]+\=[^/]+$#';

    /** @var Config */
    private $oConfig;

    /** @var Registry */
    private $oRegistry;

    /** @var Http */
    private $oHttpRequest;

    /** @var Uri */
    private $oUri;

    /** @var array */
    private $aRequestParameter = [];

    /** @var bool */
    private $bIsRouterRewritten = false;

    use Singleton; // Import the Singleton trait

    /**
     * Routing controllers.
     */
    private function __construct()
    {
        /** Objects are created for the functioning of the class **/
        $this->oConfig = Config::getInstance();
        $this->oRegistry = Registry::getInstance();
        $this->oHttpRequest = new Http;
        $this->oUri = Uri::getInstance();

        $this->indexFileRouter();

        if ($this->isAssetRequest(0) && $this->isGzipRequest(1)) {
            // Loading and compress CSS and JavaScript files
            $this->gzipRouter();
            exit;
        }

        /**
         * @internal We initialize the database after the compression of static files (self::gzipRouter() method),
         * so we can always display static files even if there are problems with the database.
         */
        $this->_initializeDatabase();

        /**
         * @internal self::initializeLanguage() method must be declared before the others, because it initializes the main language constants for the rest of the code.
         */
        $this->initializeLanguage();

        $this->checkUriCacheStatus();

        $this->initializeAssets();

        $this->launchRewritingRouter();

        $this->launchNonRewritingRouters();
    }

    /**
     *  If the module action isn't rewriting, we launch the basic router.
     */
    private function launchNonRewritingRouters()
    {
        if (!$this->isRouterRewritten()) {
            if ($this->isSimpleModuleRequest()) {
                $this->simpleModuleRouter();
            } else {
                $this->simpleRouter();
            }
        }
    }

    /**
     *  Router for the modules that are rewriting through the custom XML route file.
     *
     * @throws PH7Exception
     * @throws \PH7\Framework\File\IOException If the XML route file is not found.
     */
    private function launchRewritingRouter()
    {
        $oUrl = UriRoute::loadFile(new DomDocument);

        foreach ($oUrl->getElementsByTagName('route') as $oRoute) {
            if ($this->isRewrittenUrl($oRoute, $aMatches)) {
                $this->setRewritingRouter();

                // Get the module path from routes/*.xml file (e.g., "system/modules" or just "modules")
                $sModulePath = $oRoute->getAttribute('path') . PH7_SH;

                // Get module, from the `routes/<LANG_CODE>.xml` file
                $this->oRegistry->module = $oRoute->getAttribute('module');

                // Check if config module file exists
                if (!$this->oConfig->load(PH7_PATH_APP . $sModulePath . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
                    $this->notFound('The <b>' . $this->oRegistry->module .
                        '</b> system module is not found.<br />File: <b>' . PH7_PATH_APP . $sModulePath . $this->oRegistry->module . PH7_DS .
                        '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_APP . $sModulePath . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');
                }

                /***** PATH THE MODULE *****/
                $this->oRegistry->path_module = PH7_PATH_APP . $sModulePath . $this->oRegistry->module . PH7_DS;

                /***** URL THE MODULE *****/
                $this->oRegistry->url_module = PH7_URL_ROOT . $this->oRegistry->module . PH7_SH;

                /***** PATH THE TEMPLATE *****/
                $this->oRegistry->path_themes_module = PH7_PATH_ROOT . PH7_LAYOUT . $sModulePath . $this->oRegistry->module . PH7_DS . PH7_TPL;

                /***** URL THE TEMPLATE *****/
                $this->oRegistry->url_themes_module = PH7_RELATIVE . PH7_LAYOUT . $sModulePath . $this->oRegistry->module . PH7_SH . PH7_TPL;

                // Get the default controller
                $this->oRegistry->controller = ucfirst($oRoute->getAttribute('controller')) . self::CONTROLLER_SUFFIX;

                // Get the default action
                $this->oRegistry->action = $oRoute->getAttribute('action');
                if ($oRoute->hasAttribute('vars')) {
                    $this->generateRequestParameters($oRoute, $aMatches);
                }
                break;
            }
        }
        unset($oUrl);
    }

    /**
     * Simple Router.
     *
     * @return void
     *
     * @throws PH7Exception
     */
    private function simpleRouter()
    {
        if ($this->oUri->fragment(0) && preg_match(self::REGEX_MODULE_FORMAT, $this->oUri->fragment(0))) {
            // Set system module
            $this->oRegistry->module = $this->oUri->fragment(0);
        } else {
            // Get system module
            $this->oRegistry->module = DbConfig::getSetting('defaultSysModule');
        }

        // See if config module file exists
        if (!$this->oConfig->load(PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->notFound('The <b>' . $this->oRegistry->module . '</b> system module is not found.<br />File: <b>' . PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS .
                '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');
        }

        /***** PATH THE MODULE *****/
        $this->oRegistry->path_module = PH7_PATH_SYS . PH7_MOD . $this->oRegistry->module . PH7_DS;

        /***** URL THE MODULE *****/
        $this->oRegistry->url_module = PH7_URL_ROOT . $this->oRegistry->module . PH7_SH;

        /***** PATH THE TEMPLATE *****/
        $this->oRegistry->path_themes_module = PH7_PATH_TPL_SYS_MOD . $this->oRegistry->module . PH7_DS . PH7_TPL;

        /***** URL THE TEMPLATE *****/
        $this->oRegistry->url_themes_module = PH7_URL_TPL_SYS_MOD . $this->oRegistry->module . PH7_SH . PH7_TPL;

        if ($this->isAssetRequest(1) && $this->isAjaxRequest(2)) {
            // Loading files Asynchronous Ajax
            $this->ajaxRouter($this->oRegistry->path_module);
            exit;

        } elseif ($this->oUri->fragment(1) && preg_match(self::REGEX_CONTROLLER_FORMAT, $this->oUri->fragment(1))) {
            // Set the controller
            $this->oRegistry->controller = ucfirst($this->oUri->fragment(1)) . self::CONTROLLER_SUFFIX;
        } else {
            // Get the default controller
            $this->oRegistry->controller = ucfirst($this->oConfig->values['module']['default_controller']) . self::CONTROLLER_SUFFIX;
        }

        if ($this->oUri->fragment(2) && preg_match(self::REGEX_ACTION_FORMAT, $this->oUri->fragment(2))) {
            // Set the action
            $this->oRegistry->action = $this->oUri->fragment(2);
        } else {
            // Get the default action
            $this->oRegistry->action = $this->oConfig->values['module']['default_action'];
        }

        /** Request Parameter for the Simple Router mode. **/
        $this->addRequestParameter($this->oUri->segments(3));
    }

    /**
     * Simple Module Router.
     *
     * @return void
     *
     * @throws PH7Exception
     */
    private function simpleModuleRouter()
    {
        if ($this->oUri->fragment(1) && preg_match(self::REGEX_MODULE_FORMAT, $this->oUri->fragment(1))) {
            // Set module
            $this->oRegistry->module = $this->oUri->fragment(1);
        }

        // Check if config module file exists
        if (!$this->oConfig->load(PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE)) {
            $this->notFound('The <b>' . $this->oRegistry->module . '</b> module is not found.<br />File: <b>' . PH7_PATH_MOD . $this->oRegistry->module . PH7_DS .
                '</b><br /> or the <b>' . PH7_CONFIG_FILE . '</b> file is not found.<br />File: <b>' . PH7_PATH_MOD . $this->oRegistry->module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '</b>');
        }

        /***** PATH THE MODULE *****/
        $this->oRegistry->path_module = PH7_PATH_MOD . $this->oRegistry->module . PH7_DS;
        /***** URL THE MODULE *****/
        $this->oRegistry->url_module = PH7_URL_ROOT . 'm/' . $this->oRegistry->module . PH7_SH;
        /***** PATH THE TEMPLATE *****/
        $this->oRegistry->path_themes_module = PH7_PATH_TPL_MOD . $this->oRegistry->module . PH7_DS . PH7_TPL;
        /***** URL THE TEMPLATE *****/
        $this->oRegistry->url_themes_module = PH7_URL_TPL_MOD . $this->oRegistry->module . PH7_SH . PH7_TPL;

        if ($this->isAssetRequest(2) && $this->isAjaxRequest(3)) {
            // Loading files Asynchronous Ajax
            $this->ajaxRouter($this->oRegistry->path_module);
            exit;
        } elseif ($this->oUri->fragment(2) && preg_match(self::REGEX_CONTROLLER_FORMAT, $this->oUri->fragment(2))) {
            // Set the controller
            $this->oRegistry->controller = ucfirst($this->oUri->fragment(2)) . self::CONTROLLER_SUFFIX;
        } else {
            // Get the default controller
            $this->oRegistry->controller = ucfirst($this->oConfig->values['module']['default_controller']) . self::CONTROLLER_SUFFIX;
        }

        if ($this->oUri->fragment(3) && preg_match(self::REGEX_ACTION_FORMAT, $this->oUri->fragment(3))) {
            // Set the action
            $this->oRegistry->action = $this->oUri->fragment(3);
        } else {
            // Get the default action
            $this->oRegistry->action = $this->oConfig->values['module']['default_action'];
        }

        /** Request Parameter for the Simple Module Router mode. **/
        $this->addRequestParameter($this->oUri->segments(4));
    }

    /**
     * @return void
     */
    public function _initializeDatabase()
    {
        $aDriverOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES ' . $this->oConfig->values['database']['charset'];

        /* DSN */
        Db::getInstance(
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
     * @return void
     */
    public function _unsetDatabaseInfo()
    {
        unset($this->oConfig->values['database']);
    }

    /**
     * Internationalization with Gettext.
     *
     * @return void
     *
     * @throws \PH7\Framework\Translate\Exception
     */
    private function initializeLanguage()
    {
        if (!defined('PH7_PREF_LANG')) {
            define('PH7_PREF_LANG', DbConfig::getSetting('defaultLanguage'));
        }

        if (!defined('PH7_LANG_NAME')) {
            // Set the default language of the site and load the default language path
            $sLangName = (new Lang)
                ->setDefaultLang(PH7_PREF_LANG)
                ->init()
                ->load(self::MAIN_GETTEXT_FILENAME, PH7_PATH_APP_LANG)
                ->getLocaleName();

            define('PH7_LANG_NAME', $sLangName);
        }

        /*** Get the ISO language code (the two first letters) ***/
        define('PH7_DEFAULT_LANG_CODE', Lang::getIsoCode(PH7_DEFAULT_LANG));
        define('PH7_LANG_CODE', Lang::getIsoCode(PH7_LANG_NAME));

        /*** Set locale environment variables for gettext ***/
        putenv('LC_ALL=' . PH7_LANG_NAME);
        setlocale(LC_ALL, PH7_LANG_NAME);
    }

    /**
     * @return void
     *
     * @throws \PH7\Framework\Layout\Exception
     */
    private function initializeTemplate()
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
     * @return void
     */
    private function initializePaths()
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
     * @return void
     *
     * @throws PH7Exception
     * @throws \PH7\Framework\Http\Exception
     */
    private function initializeAssets()
    {
        if ($this->isAssetRequest(0)) {
            switch ($this->oUri->fragment(1)) {
                case self::AJAX_REQUEST_PARAM_NAME:
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
                    $this->notFound(
                        'Asset file not found!',
                        self::REDIRECT_ERROR_MOD
                    );
            }
            exit;
        }
    }

    /**
     * Check if the URI cache needs to be regenerated when outdated.
     *
     * @return void
     */
    private function checkUriCacheStatus()
    {
        if (UriRoute::URI_CACHE_ENABLED && UriRoute::isCachedUrlOutdated()) {
            UriRoute::clearCache();
        }
    }

    /**
     * @return void
     *
     * @throws \PH7\Framework\Layout\Gzip\Exception If the cache file couldn't be written.
     * @throws \PH7\Framework\Layout\Gzip\Exception If the file couldn't be read.
     */
    private function gzipRouter()
    {
        (new Gzip)->run();
    }

    /**
     * @param string|null $sMod
     *
     * @return void
     *
     * @throws PH7Exception
     */
    private function ajaxRouter($sMod = null)
    {
        // Load Ajax class for jsonMsg() func
        FileImporter::pH7FwkClass('Ajax.Ajax');

        // Option for Content Type
        if ($this->oHttpRequest->getExists('option')) {
            if ($this->oHttpRequest->get('option') === 'plain') {
                header('Content-Type: text/plain; charset=utf-8');
            }
        }

        // For module only!
        if (!empty($sMod)) {
            $this->initializePaths();

            $sFolder = ($this->oUri->fragment(4) && preg_match(self::REGEX_FOLDER_FORMAT, $this->oUri->fragment(4))) ? PH7_DS . $this->oUri->fragment(4) : '';
            if (is_file($sMod . 'assets/ajax/' . $this->oUri->fragment(3) . $sFolder . 'Ajax.php')) {
                include_once $sMod . 'assets/ajax/' . $this->oUri->fragment(3) . $sFolder . 'Ajax.php';
            } else {
                $this->notFound(
                    'Error while loading the library of module ajax<br />File: ' . $sMod . 'assets' . PH7_DS . 'ajax' . PH7_DS . $this->oUri->fragment(3) . $sFolder . 'Ajax.php does not exist',
                    self::REDIRECT_ERROR_MOD
                );
            }
        } else {
            $sFolder = ($this->oUri->fragment(3) && preg_match(self::REGEX_FOLDER_FORMAT, $this->oUri->fragment(3))) ? PH7_DS . $this->oUri->fragment(3) : '';

            if (is_file(PH7_PATH_SYS . 'core/assets/ajax/' . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php')) {
                include_once PH7_PATH_SYS . 'core/assets/ajax/' . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php';
            } else {
                $this->notFound(
                    'Error while loading the library of ajax<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'ajax' . PH7_DS . $this->oUri->fragment(2) . $sFolder . 'CoreAjax.php does not exist',
                    self::REDIRECT_ERROR_MOD
                );
            }
        }
    }

    /**
     * @throws PH7Exception
     */
    private function fileRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/file/' . $this->oUri->fragment(2) . 'CoreFile.php')) {
            include_once PH7_PATH_SYS . 'core/assets/file/' . $this->oUri->fragment(2) . 'CoreFile.php';
        } else {
            $this->notFound(
                'Error while loading the file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'file' . PH7_DS . $this->oUri->fragment(2) . 'CoreFile.php does not exist',
                self::REDIRECT_ERROR_MOD
            );
        }
    }

    /**
     * @throws PH7Exception
     * @throws \PH7\Framework\Http\Exception
     */
    private function cronRouter()
    {
        if ($this->isCronHashValid()) {
            if (is_file(PH7_PATH_SYS . 'core/assets/cron/' . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php')) {
                ignore_user_abort(true);
                require PH7_PATH_SYS . 'core/assets/cron/' . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php';
            } else {
                $this->notFound(
                    'Error while loading the Cron Jobs file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'cron' . PH7_DS . $this->oUri->fragment(2) . PH7_DS . $this->oUri->fragment(3) . 'CoreCron.php does not exist',
                    self::REDIRECT_ERROR_MOD
                );
            }
        } else {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);
            exit('Secret word is invalid for the cron hash!');
        }
    }

    /**
     * Check if the cron's security string is valid or not.
     *
     * @return bool
     */
    private function isCronHashValid()
    {
        return strcmp(
                $this->oHttpRequest->get('secret_word'),
                DbConfig::getSetting('cronSecurityHash')
            ) === 0;
    }

    /**
     * @throws PH7Exception
     */
    private function cssRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/css/' . $this->oUri->fragment(2) . 'CoreCss.php')) {
            header('Content-Type: text/css');
            include_once PH7_PATH_SYS . 'core/assets/css/' . $this->oUri->fragment(2) . 'CoreCss.php';
        } else {
            $this->notFound(
                'Error while loading the Javascript file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'css' . PH7_DS . $this->oUri->fragment(2) . 'CoreCss.php does not exist',
                self::REDIRECT_ERROR_MOD
            );
        }
    }

    /**
     * @throws PH7Exception
     */
    private function jsRouter()
    {
        if (is_file(PH7_PATH_SYS . 'core/assets/js/' . $this->oUri->fragment(2) . 'CoreJs.php')) {
            header('Content-Type: text/javascript');
            include_once PH7_PATH_SYS . 'core/assets/js/' . $this->oUri->fragment(2) . 'CoreJs.php';
        } else {
            $this->notFound(
                'Error while loading the Javascript file<br />File: ' . PH7_PATH_SYS . 'core' . PH7_DS . 'assets' . PH7_DS . 'js' . PH7_DS . $this->oUri->fragment(2) . 'CoreJs.php does not exist',
                self::REDIRECT_ERROR_MOD
            );
        }
    }

    /**
     * @throws PH7Exception
     */
    public function runRouter()
    {
        $this->initializePaths();

        /***** FOR FILE CONFIG .INI OF MODULE *****/
        $this->oConfig->load($this->oRegistry->path_module . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        // PH7_DEFAULT_TPL_MOD constant has to be defined before calling "initializeTemplate()"
        define('PH7_DEFAULT_TPL_MOD', $this->oConfig->values['module']['default_theme']);

        $this->initializeTemplate();

        if ($this->doesControllerModuleExist()) {
            if ($this->doesModuleHaveBootstrap()) {
                require_once $this->oRegistry->path_module . 'Bootstrap.php';
            }

            $this->runController();
        } else {
            $this->notFound('The <b>' . $this->oRegistry->controller . '</b> controller of the <b>' . $this->oRegistry->module .
                '</b> module is not found.<br />File: <b>' . $this->oRegistry->path_module . '</b>',
                self::REDIRECT_ERROR_MOD
            );
        }
    }

    /**
     * Check if the module's action is rewriting by the XML route file or not.
     *
     * @return bool
     */
    private function isRouterRewritten()
    {
        return $this->bIsRouterRewritten;
    }

    /**
     * If the action is rewriting by the XML route file, set the correct router to be used.
     *
     * @return void
     */
    private function setRewritingRouter()
    {
        $this->bIsRouterRewritten = true;
    }

    /**
     * Run the module's controller (or display an error message if the controller doesn't exist).
     *
     * @return void
     */
    private function runController()
    {
        $sController = self::PROJECT_NAMESPACE . $this->oRegistry->controller;

        try {
            $oMvc = new ReflectionMethod($sController, $this->oRegistry->action);
            if ($oMvc->isPublic()) {
                // Perform the controller's action
                $oMvc->invokeArgs(new $sController, $this->getRequestParameter());
            } else {
                $this->notFound(
                    'The <b>' . $this->oRegistry->action . '</b> method is not public!',
                    self::REDIRECT_ERROR_MOD
                );
            }

            // Destruct object to minimize CPU resources
            unset($oMvc);
        } catch (ReflectionException $oExcept) {
            // If the class or method doesn't exist
            $this->notFound(
                $oExcept->getMessage(),
                self::REDIRECT_ERROR_MOD
            );
        }
    }

    /**
     * @param DOMElement $oRoute
     * @param array $aMatches
     *
     * @return bool
     */
    private function isRewrittenUrl(DOMElement $oRoute, &$aMatches)
    {
        return preg_match(
            '`^' . $oRoute->getAttribute('url') . self::REGEX_URL_EXTRA_OPTIONS . '$`',
            $this->oHttpRequest->requestUri(),
            $aMatches
        );
    }

    private function generateRequestParameters(DOMElement $oRoute, array $aMatches)
    {
        $aVars = explode(UriRoute::VARS_PARAM_DELIMITER, $oRoute->getAttribute('vars'));
        $iOffset = count($aVars);

        foreach ($aMatches as $sKey => $sMatch) {
            if ($sKey !== 0) {
                $this->oHttpRequest->setGet($aVars[$sKey - 1], $sMatch);

                /** Request Parameter for the Router Rewriting mode **/
                $this->addRequestParameter(
                    $this->oUri->segments($this->oUri->totalFragment() - $iOffset)
                );
            }
        }
    }

    /**
     * Get the Request Parameters.
     *
     * @return array The Request Parameters if it exists, otherwise an empty array.
     */
    private function getRequestParameter()
    {
        $aRequest = [];

        if ($this->doRequestParameterExist()) {
            foreach ($this->aRequestParameter as $sVal) {
                $sVal = trim($this->secureRequestParameter($sVal));

                if ($sVal !== '') {
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
     * @param string $sVal The request action name to clean.
     *
     * @return string
     */
    private function cleanSlugUrl($sVal)
    {
        return preg_replace(self::REGEX_URL_PARAMS, '', $sVal);
    }

    /**
     * Secures the Request Parameter.
     *
     * @param string $sVar
     *
     * @return string
     */
    private function secureRequestParameter($sVar)
    {
        $sVar = escape($sVar, true);

        // Convert characters to entities and return them
        $aBadCharacters = [
            '$',
            '(',
            ')',
            '%28',
            '%29'
        ];
        $aGoodCharacters = [
            '&#36;',
            '&#40;',
            '&#41;',
            '&#40;',
            '&#41;'
        ];

        return str_replace(
            $aBadCharacters,
            $aGoodCharacters,
            $sVar
        );
    }

    /**
     * Remove the Request Parameter variable.
     *
     * @return void
     */
    private function clearRequestParameter()
    {
        unset($this->aRequestParameter);
    }

    private function addRequestParameter(array $aRequestParameter)
    {
        $this->aRequestParameter = $aRequestParameter;
    }

    /**
     * @return bool
     */
    private function doRequestParameterExist()
    {
        return count($this->aRequestParameter) > 0;
    }

    /**
     * @return bool
     */
    private function isSimpleModuleRequest()
    {
        return $this->oUri->fragment(0) === self::SIMPLE_MOD_REQUEST_PARAM_NAME;
    }

    /**
     * @param int $iKey
     *
     * @return bool
     */
    private function isAssetRequest($iKey)
    {
        return $this->oUri->fragment($iKey) === self::ASSET_REQUEST_PARAM_NAME;
    }

    /**
     * @param int $iKey
     *
     * @return bool
     */
    private function isAjaxRequest($iKey)
    {
        return $this->oUri->fragment($iKey) === self::AJAX_REQUEST_PARAM_NAME;
    }

    /**
     * @param int $iKey
     *
     * @return bool
     */
    private function isGzipRequest($iKey)
    {
        return $this->oUri->fragment($iKey) === self::GZIP_REQUEST_PARAM_NAME;
    }

    /**
     * @return bool
     */
    private function doesControllerModuleExist()
    {
        return is_file($this->oRegistry->path_module_controllers . $this->oRegistry->controller . '.php');
    }

    /**
     * @return bool
     */
    private function doesModuleHaveBootstrap()
    {
        return is_file($this->oRegistry->path_module . 'Bootstrap.php');
    }

    /**
     * We display an error page if someone request "index.php" filename in order to avoid disclosing and explicitly request the PHP index filename (e.g. for security reasons...).
     * Otherwise, if the URL rewrite extension is not enabled, we redirect the page to index.php file (then [URL]/index.php/[REQUEST]/ ).
     *
     * @see self::notFound()
     *
     * @return void
     *
     * @throws PH7Exception
     */
    private function indexFileRouter()
    {
        // The following code will be useless if pH7CMS will be able to work without mod_rewrite
        if ($this->oHttpRequest->currentUrl() === PH7_URL_ROOT . static::INDEX_FILE) {
            $this->notFound('In "production" mode, it simulates "404 page not found" if the index.php filename is called, to avoid disclosing the language index filename (e.g. for security reasons...).');
        }
    }

    /**
     * This method has two different behaviors depending of the environment mode site.
     * 1. In production mode: Displays the page not found using the system module "error".
     * 2. In development mode: It throws an Exception with displaying an explanatory message that indicates why this page was not found.
     *
     * @param string $sMsg
     * @param string $iRedirect 1 = redirect
     *
     * @return void
     *
     * @throws PH7Exception If the site is in development mode, displays an explanatory message that indicates why this page was not found.
     */
    private function notFound($sMsg = null, $iRedirect = null)
    {
        if ($sMsg !== null && isDebug()) {
            throw new PH7Exception($sMsg);
        }

        if ($iRedirect === null) {
            $this->oRegistry->module = 'error';

            // Reload the config.ini file for the "error" module
            $this->oConfig->load(PH7_PATH_SYS . PH7_MOD . 'error' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);
        } else {
            Header::redirect(
                UriRoute::get(
                    'error',
                    'http',
                    'index'
                )
            );
        }
    }
}
