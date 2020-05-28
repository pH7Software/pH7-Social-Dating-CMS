<?php
/***************************************************************************
 * @title            PH7 Template Engine
 * @desc             Template Engine with Compiler and Cache for pH7 CMS!
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @category         PH7 Template Engine
 * @package          PH7 / Framework / Layout / Tpl / Engine / PH7Tpl
 * @copyright        (c) 2011-2020, Pierre-Henry Soria. All Rights Reserved.
 * @version          1.4.1
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 *
 * @history          Supports now PHP 5 with beautiful object code (POO), (removed all the ugly object code from PHP 4.x).
 * @history          Supports now PHP 5.3 (added namespace and incorporate the template engine into the pH7Framework).
 * @history          Supports PHP 5.4 (added class member access on instantiation, e.g. (new Foo)->bar(), ...).
 ***************************************************************************/

namespace PH7\Framework\Layout\Tpl\Engine\PH7Tpl;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Compress\Compress;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\File\GenerableFile;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Mail as MailLayout;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Exception as TplException;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\Syntax\Syntax;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mvc\Model\Design as DesignModel;
use PH7\Framework\Parse\SysVar;

class PH7Tpl extends Kernel implements Templatable, GenerableFile
{
    const NAME = 'PH7Tpl';
    const AUTHOR = 'Pierre-Henry Soria';
    const VERSION = '1.4.1';
    const LICENSE = 'Creative Commons Attribution 3.0 License - http://creativecommons.org/licenses/by/3.0/';
    const ERR_MSG = 'It seems you have removed the copyright notice(s) in the software. If you really want to remove them, please email: %s';
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @internal For better compatibility with Windows, we didn't put a slash at the end of the directory constants.
     */
    const COMPILE_DIR = 'pH7tpl_compile';
    const CACHE_DIR = 'pH7tpl_cache';
    const MAIN_COMPILE_DIR = 'public_main';

    const MAIN_PAGE = 'layout';
    const MAIN_COMPILE_PAGE = 'layout.cpl.php';
    const XML_SITEMAP_COMPILE_PAGE = 'mainlayout.xsl.cpl.php';
    const TEMPLATE_FILE_EXT = '.tpl';
    const COMPILE_FILE_EXT = '.cpl.php';
    const CACHE_FILE_EXT = '.cache.html';

    const RESERVED_WORDS = [
        'auto_include',
        'def_main_auto_include',
        'else',
        'literal',
        'lang'
    ];

    /** @var DesignModel */
    private $designModel;

    /** @var Syntax */
    private $oSyntaxEngine;

    /** @var string */
    private $sTplFile;

    /** @var string */
    private $sTemplateDir;

    /** @var string */
    private $sCompileDir;

    /** @var string */
    private $sCompileDir2;

    /** @var string */
    private $sCacheDir;

    /** @var string */
    private $sCacheDir2;

    /** @var string */
    private $sCode;

    /** @var string */
    private $sTemplateDirFile;

    /** @var string */
    private $sCompileDirFile;

    /** @var string */
    private $sCacheDirFile;

    /** @var bool */
    private $bCaching = false;

    /** @var bool */
    private $bHtmlCompressor;

    /** @var bool */
    private $bPhpCompressor;

    /** @var int|null */
    private $mCacheExpire;

    /** @var array */
    private $_aVars = [];

    /** @var PH7Tpl */
    private $_oVars;

    // Hack that keeps the $config variable in the template files
    protected $config;

    public function __construct(Syntax $oSyntaxEngine)
    {
        parent::__construct();

        $this->checkCompileDir();
        $this->checkCacheDir();

        /** Instance objects to the class **/
        $this->_oVars = $this;
        $this->designModel = new DesignModel;
        $this->oSyntaxEngine = new $oSyntaxEngine;

        $this->bHtmlCompressor = (bool)$this->config->values['cache']['enable.static.minify'];
        $this->bPhpCompressor = (bool)$this->config->values['cache']['enable.static.minify'];
    }

    /**
     * Get the main page file of the template.
     *
     * @return string The main page file.
     */
    public function getMainPage()
    {
        return static::MAIN_PAGE . static::TEMPLATE_FILE_EXT;
    }

    /**
     * Set the directory for the template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setTemplateDir($sDir)
    {
        if (!is_dir($sDir)) {
            throw new PH7InvalidArgumentException(
                sprintf('<strong>%s</strong> cannot find "%s" template directory.', self::NAME, $sDir)
            );
        }

        $this->sTemplateDir = $this->file->checkExtDir($sDir);
    }

    /**
     * Set the directory for the compilation template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCompileDir($sDir)
    {
        if (!is_dir($sDir)) {
            throw new PH7InvalidArgumentException(
                sprintf(
                    '<strong>%s</strong> cannot find "%s" compile directory.', self::NAME, $sDir)
            );
        }

        $this->sCompileDir = $this->file->checkExtDir($sDir);
    }

    /**
     * Set the directory for the cache template.
     *
     * @param string $sDir
     *
     * @return void
     *
     * @throws PH7InvalidArgumentException An explanatory message if the directory does not exist.
     */
    public function setCacheDir($sDir)
    {
        if (!is_dir($sDir)) {
            throw new PH7InvalidArgumentException(
                sprintf('<strong>%s</strong> cannot find "%s" cache directory.', self::NAME, $sDir)
            );
        }

        $this->sCacheDir = $this->file->checkExtDir($sDir);
    }

    /**
     * Enabled the cache.
     *
     * @param bool $bCaching
     *
     * @return void
     */
    public function setCaching($bCaching)
    {
        $this->bCaching = (bool)$bCaching;
    }

    /**
     * Check if the cache is enabled.
     *
     * @return bool
     */
    public function isEnableCache()
    {
        return $this->bCaching;
    }

    /**
     * Set the HTML Compressor.
     *
     * @param bool $bCompressor
     *
     * @return void
     */
    public function setHtmlCompress($bCompressor)
    {
        $this->bHtmlCompressor = (bool)$bCompressor;
    }

    /**
     * Set the PHP Compressor.
     *
     * @param bool $bCompressor
     *
     * @return void
     */
    public function setPhpCompress($bCompressor)
    {
        $this->bPhpCompressor = (bool)$bCompressor;
    }

    /**
     * Set the time of expire cache.
     *
     * @param int $iLifeTime In seconds.
     *
     * @return void
     */
    public function setCacheExpire($iLifeTime)
    {
        $this->mCacheExpire = (int)$iLifeTime; // 3600 seconds = 1 hour cache duration
    }

    /**
     * Adds a variable that can be used by the templates.
     * Adds a new array index to the variable property. This
     * new array index will be treated as a variable by the templates.
     *
     * @see pH7Tpl::assign()
     *
     * @param string $sName The variable name to use in the template
     * @param mixed $mValue (string, object, array, integer, ...) Value Variable
     *
     * @return void
     */
    public function __set($sName, $mValue)
    {
        $this->assign($sName, $mValue);
    }

    /**
     * Retrieve an assigned variable (overload the magic __get method).
     *
     * @see pH7Tpl::getVar()
     *
     * @param string $sKey The variable name.
     *
     * @return mixed The variable value.
     */
    public function __get($sKey)
    {
        return $this->getVar($sKey);
    }

    /**
     * Allows testing with empty() and isset() to work.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function __isset($sKey)
    {
        return isset($this->_aVars[$sKey]);
    }

    /**
     * @param string $sTplFile Default NULL
     * @param string $sDirPath Default NULL
     * @param bool $bInclude Default TRUE
     *
     * @return string
     *
     * @throws TplException If the template file does no exist.
     * @throws PH7InvalidArgumentException
     */
    public function display($sTplFile = null, $sDirPath = null, $bInclude = true)
    {
        $this->sTplFile = $sTplFile;

        if (!empty($sDirPath)) {
            $this->setTemplateDir($sDirPath);
        }

        $this->sTemplateDirFile = $this->sTemplateDir . 'tpl' . PH7_DS . $this->sTplFile;
        $bIsMainDir = $this->isMainDir($sDirPath);
        $sCurrentController = $this->getCurrentController();
        $sTplFileName = $this->file->getFileWithoutExt($this->sTplFile);

        $this->file->createDir($this->sCompileDir);

        if ($bIsMainDir) {
            $this->sCompileDir2 = $this->sCompileDir . static::MAIN_COMPILE_DIR . PH7_DS . PH7_TPL_NAME . PH7_DS;
        } else {
            $this->sCompileDir2 = $this->sCompileDir . $this->registry->module . '_' . md5($this->registry->path_module) . PH7_DS . PH7_TPL_MOD_NAME . PH7_DS . $sCurrentController . PH7_DS;
        }

        $this->sCompileDirFile = ($bIsMainDir ? $this->sCompileDir2 . $sTplFileName . static::COMPILE_FILE_EXT : $this->sCompileDir2) .
            str_replace($sCurrentController, '', $sTplFileName) . static::COMPILE_FILE_EXT;

        if (!$this->file->existFile($this->sTemplateDirFile)) {
            throw new TplException(
                sprintf('%s file does no exist.', $this->sTemplateDirFile)
            );
        }


        /*** If the file does not exist or if the template has been modified, recompile the makefiles ***/
        if ($this->file->getModifTime($this->sTemplateDirFile) > $this->file->getModifTime($this->sCompileDirFile)) {
            $this->compile();
        }

        if ($bInclude) {
            $bCaching = (bool)$this->config->values['cache']['enable.html.tpl.cache'];

            if ($bCaching === true && $this->isEnableCache() === true && !$this->isMainCompilePage()) {
                $this->cache();
            } else {
                // Extraction Variables
                extract($this->_aVars);
                require $this->sCompileDirFile;
            }
        } else {
            return $this->sCompileDirFile;
        }
    }

    /**
     * Parse an email template.
     *
     * @param string $sMailTplFile
     * @param string $sEmailAddress It is used to create the privacy policy for lute against spam.
     *
     * @return string The contents of the template parsed.
     *
     * @throws TplException If the template file could not be opened.
     */
    public function parseMail($sMailTplFile, $sEmailAddress)
    {
        /**
         * If the template doesn't contain theme for emails, we retrieve the emails default themes.
         */
        if (defined('PH7_TPL_NAME') && !is_file($sMailTplFile)) {
            $sMailTplFile = str_replace(PH7_TPL_NAME, PH7_DEFAULT_THEME, $sMailTplFile);
        }

        if (!$sCode = $this->file->getFile($sMailTplFile)) {
            throw new TplException(
                sprintf('Cannot open "%s" file.', $sMailTplFile)
            );
        }

        /***** Other variables in file "/framework/Parse/SysVar.class.php" with syntax %var% *****/
        $sCode = (new SysVar)->parse($sCode);

        foreach ($this->_aVars as $sKey => $sValue) {
            /*** Variables ***/

            // We can't convert an object to a string with str_replace, which we tested the variables with is_object function
            if (!is_object($sValue)) {
                $sCode = str_replace('{' . $sKey . '}', $sValue, $sCode);
            }

            // Email Address
            //$sCode = str_replace('{email}', $sEmailAddress, $sCode);

            $oMailDesign = new MailLayout;

            /* Headers */

            // Includes
            $sCode = str_replace('{inc_header}', $oMailDesign->header(), $sCode);
            $sCode = str_replace('{inc_sub_header}', $oMailDesign->subHeader(), $sCode);

            /* Footers */

            // Privacy Policy Footer
            $sCode = str_replace('{pp_footer}', $oMailDesign->privacyPolicyFooter($sEmailAddress), $sCode);

            // Bottom Footer
            $sCode = str_replace('{b_footer}', $oMailDesign->bottomFooter(), $sCode);

            // Includes
            $sCode = str_replace('{inc_sub_footer}', $oMailDesign->subFooter($sEmailAddress), $sCode);
            $sCode = str_replace('{inc_footer}', $oMailDesign->footer(), $sCode);
            unset($oMailDesign);
        }

        return $sCode;
    }

    /**
     * Assign variables to the template.
     *
     *
     * @example
     *
     * Example with a string variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('var_name', $sName);
     *
     * === TPL ===
     *     {var_name}
     * </code>
     *
     *
     * Example with an array variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('arr_data_var', $aData);
     *
     * === TPL ===
     *     {% $arr_data_var['key1'] %}
     * </code>
     *
     *
     * Example with an object variable:
     *
     * <code>
     * === PHP ===
     *     $oPh7Tpl->assign('obj_user_var', $oUser);
     *
     * === TPL ===
     *     {% $obj_user_var->getUsers() %}
     * --- OR ---
     *      {{ $obj_user_var->printUsers() }}
     * </code>
     *
     *
     * @see __set()
     *
     * @param string $sName Variable name
     * @param mixed $mValue (string, object, array, integer, ...) Value Variable
     * @param bool $bEscape Specify "true" if you want to protect your variables against XSS.
     * @param bool $bEscapeStrip If you use escape method, you can also set this parameter to "true" to strip HTML and PHP tags from a string.
     *
     * @return void
     */
    public function assign($sName, $mValue, $bEscape = false, $bEscapeStrip = false)
    {
        if ($bEscape === true) {
            $mValue = $this->str->escape($mValue, $bEscapeStrip);
        }

        $this->_aVars[$sName] = $mValue;
    }

    /**
     * Assign variables from array.
     *
     * @see assign()
     *
     * @param array $aVars
     * @param bool $bEscape Specify TRUE if you want to protect your variables against XSS.
     * @param bool $bEscapeStrip If you use escape method, you can also set this parameter to "true" to strip HTML and PHP tags from a string.
     *
     * @return void
     */
    public function assigns(array $aVars, $bEscape = false, $bEscapeStrip = false)
    {
        foreach ($aVars as $sKey => $sValue) {
            $this->assign($sKey, $sValue, $bEscape, $bEscapeStrip); // Assign a string variable
        }
    }

    /**
     * Get a variable we assigned with the assign() method.
     *
     * @see __get()
     *
     * @param $sVarName string Name of a variable that is to be retrieved.
     *
     * @return mixed Value of that variable.
     */
    public function getVar($sVarName)
    {
        return isset($this->_aVars[$sVarName]) ? $this->_aVars[$sVarName] : '';
    }

    /**
     * Remove all variables from memory template.
     *
     * @return void
     */
    public function clean()
    {
        unset($this->_aVars, $this->_oVars);
    }

    /**
     * Get the reserved variables.
     *
     * @return array
     */
    public function getReservedWords()
    {
        return self::RESERVED_WORDS;
    }

    /**
     * Get the header content to put in the file.
     *
     * @return string
     */
    final public function getHeaderContents()
    {
        return '
namespace PH7;
defined(\'PH7\') or exit(\'Restricted access\');
/*
Created on ' . gmdate(self::DATETIME_FORMAT) . '
Compiled file from: ' . $this->sTemplateDirFile . '
Template Engine: ' . self::NAME . ' version ' . self::VERSION . ' by ' . self::AUTHOR . '
*/
/***************************************************************************
 *     ' . self::SOFTWARE_NAME . ' ' . self::SOFTWARE_COMPANY . '
 *               --------------------
 * @since      Mon Mar 21 2011
 * @author     Pierre-Henry Soria
 * @email      ' . self::SOFTWARE_EMAIL . '
 * @link       ' . self::SOFTWARE_WEBSITE . '
 * @copyright  ' . sprintf(self::SOFTWARE_COPYRIGHT, date('Y')) . '
 * @license    ' . self::LICENSE . '
 ***************************************************************************/
';
    }

    /**
     * Set self pointer on cloned object.
     *
     * @clone
     */
    public function __clone()
    {
        $this->_oVars = $this;
    }

    /**
     * Cache system for the static contents with support for different templates and languages!
     *
     * @return void
     *
     * @throws Exception
     * @throws \PH7\Framework\File\Permission\PermissionException
     * @throws TplException If the cache file could not be written.
     */
    protected function cache()
    {
        // Create cache folder
        $this->file->createDir($this->sCacheDir);

        $this->sCacheDir2 = $this->sCacheDir . PH7_TPL_NAME . PH7_DS . $this->registry->module . '_' . md5($this->
            registry->path_module) . PH7_DS . PH7_TPL_MOD_NAME . PH7_DS . PH7_LANG_NAME . PH7_DS . $this->getCurrentController() . PH7_DS;
        $this->file->createDir($this->sCacheDir2);
        $this->sCacheDirFile = $this->sCacheDir2 . str_replace(PH7_DS, '_', $this->file->getFileWithoutExt($this->sTplFile)) . static::CACHE_FILE_EXT;

        if ($this->hasCacheExpired()) {
            ob_start();

            // Extraction Variables
            extract($this->_aVars);

            require $this->sCompileDirFile;
            $sOutput = ob_get_contents();
            ob_end_clean();

            if ($this->bHtmlCompressor) {
                $sOutput = (new Compress)->parseHtml($sOutput);
            }

            if (!$this->file->putFile($this->sCacheDirFile, $sOutput)) {
                throw new TplException(
                    sprintf('Unable to write HTML cached file "%s"', $this->sCacheDirFile)
                );
            }

            echo $sOutput;
        } else {
            readfile($this->sCacheDirFile);
        }
    }

    /**
     * Optimizes the code generated by pH7Tpl syntax parser.
     *
     * @return void
     */
    protected function optimizeCode()
    {
        $this->sCode = preg_replace(['#[\t\r\n];?\?>#s', '#\?>[\t\r\n]+?<\?(php)?#si'], '', $this->sCode);
        $this->sCode = preg_replace('#;{2,}#s', ';', $this->sCode);
    }

    /**
     * Get current pH7CMS's controller.
     *
     * @return string The current controller
     */
    protected function getCurrentController()
    {
        return $this->httpRequest->currentController();
    }

    /**
     * Compiler template.
     *
     * @return bool
     *
     * @throws TplException If the template file could not be recovered or cannot be written.
     */
    final private function compile()
    {
        // Create compile folder
        $this->file->createDir($this->sCompileDir2);

        if (!$this->sCode = $this->file->getFile($this->sTemplateDirFile)) {
            throw new TplException(
                sprintf('Impossible to fetch template file "%s"', $this->sTemplateDirFile)
            );
        }

        // Parser the predefined variables
        $this->sCode = (new Predefined\Variable($this->sCode))->assign()->get();

        // Parser the predefined template functions
        $this->sCode = (new Predefined\Func($this->sCode))->assign()->get();

        // Parser the language constructs
        $this->parse();

        $sPhpHeader = $this->getHeaderContents();

        // Check if the "$design" variable is actually part of the \PH7\Framework\Layout\Html\Design class
        if (!$this->checkDesignInstance()) {
            $this->setErrMsg();
        }

        /**
         * Skip this step if it's not layout.tpl file or if it's not the base template
         * (because there isn't "link()" in layout.tpl of other templates as it includes the "base" one).
         */
        if ($this->isMainCompilePage() && !$this->notBaseTheme()) {
            // It is forbidden to violate the copyright!
            // Think to me, who has spent years to develop a professional, high-quality software and done my best to help other developers!
            if (!$this->isMarkCopyright()) {
                $this->setErrMsg();
            }
        }

        if ($this->isXmlSitemapCompilePage() && !$this->isSmallMarkCopyright()) {
            $this->setErrMsg();
        }

        if ($this->bPhpCompressor) {
            $this->sCode = (new Compress)->parsePhp($this->sCode);
        }

        $this->sCode = '<?php ' . $sPhpHeader . '?>' . $this->sCode;

        if ($rHandle = @fopen($this->sCompileDirFile, 'wb')) {
            fwrite($rHandle, $this->sCode);
            fclose($rHandle);
            return true;
        }

        throw new TplException(
            sprintf('Could not write template compiled file "%s"', $this->sCompileDirFile)
        );
    }

    /**
     * Parse the template syntax code for translating the language template to PHP.
     *
     * @return void
     */
    private function parse()
    {
        $this->oSyntaxEngine->setCode($this->sCode);
        $this->oSyntaxEngine->setShortcutsToObjects();

        /***** Parse pH7Tpl's syntax *****/
        $this->oSyntaxEngine->setTemplateFile($this->sTplFile);
        $this->oSyntaxEngine->parse();

        $this->sCode = $this->oSyntaxEngine->getParsedCode();

        /***** Code optimization *****/
        $this->optimizeCode();
    }

    /**
     * Checks if the template file in the $this->sTemplateDirFile attribute is the main page (layout.tpl).
     *
     * @return bool
     */
    private function isMainPage()
    {
        return preg_match('#' . $this->addSlashes(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS . $this->getMainPage()) . '#', $this->sTemplateDirFile);
    }

    /**
     * Checks if the compile file in the $this->sCompileDirFile attribute is the main page (layout.cpl.php).
     *
     * @return bool
     */
    final private function isMainCompilePage()
    {
        return preg_match(
            '#' . $this->addSlashes($this->sCompileDir . static::MAIN_COMPILE_DIR . PH7_DS . PH7_TPL_NAME . PH7_DS . static::MAIN_COMPILE_PAGE) . '#',
            $this->sCompileDirFile
        );
    }

    /**
     * Checks if the compile file in the $this->sCompileDirFile attribute is the XML (with XSL layout) Sitemap page (mainlayout.xsl.cpl.php).
     *
     * @return bool
     */
    final private function isXmlSitemapCompilePage()
    {
        return preg_match('#' . static::XML_SITEMAP_COMPILE_PAGE . '#', $this->sCompileDirFile);
    }

    /**
     * Checks if the directory passed by the argument of the method is the main directory.
     *
     * @param string $sDirPath
     *
     * @return bool
     */
    final private function isMainDir($sDirPath)
    {
        return is_dir($sDirPath) && preg_match('#' . $this->addSlashes(PH7_PATH_TPL . PH7_TPL_NAME . PH7_DS) . '#', $sDirPath);
    }

    /**
     * Check that the variable "$design" is actually parts of the Design class.
     *
     * @return bool
     */
    final private function checkDesignInstance()
    {
        return !empty($this->_aVars['design']) && $this->_aVars['design'] instanceof Design;
    }

    /**
     * Checks if the marks licensing, copyright has not been removed.
     *
     * @return bool
     */
    final private function isMarkCopyright()
    {
        // "link()" and "softwareComment()" can never be removed
        return $this->isKeywordFoundInCode('design->link()') &&
            $this->isKeywordFoundInCode('design->softwareComment()');
    }

    /**
     * Checks if the small links copyright has not been removed.
     *
     * @return bool
     */
    final private function isSmallMarkCopyright()
    {
        return $this->isKeywordFoundInCode('design->smallLink()');
    }

    /**
     * Check if it's not the base theme.
     *
     * @return bool Returns TRUE if it's not the base theme, FALSE otherwise.
     */
    final private function notBaseTheme()
    {
        return strpos($this->sTemplateDir, PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS) === false &&
            $this->isKeywordFoundInCode('$this->display(\'' . $this->getMainPage() . '\', PH7_PATH_TPL . PH7_DEFAULT_THEME . PH7_DS)');
    }

    /**
     * @return bool Returns TRUE if the cache has expired, FALSE otherwise.
     */
    private function hasCacheExpired()
    {
        return
            $this->file->getModifTime($this->sCompileDirFile) > $this->file->getModifTime($this->sCacheDirFile) ||
            (!empty($this->mCacheExpire) && $this->file->getModifTime($this->sCacheDirFile) < time() - $this->mCacheExpire);
    }

    /**
     * Add slashes to avoid errors with "preg_replace()" with Windows' backslashes in directories.
     *
     * @param string $sStr
     *
     * @return string Escaped string
     */
    private function addSlashes($sStr)
    {
        return addslashes($sStr);
    }

    /**
     * Checks if the compile directory has been defined otherwise we create a default directory.
     *
     * If the folder compile does not exist, it creates a folder.
     *
     * @return self
     */
    private function checkCompileDir()
    {
        $this->sCompileDir = empty($this->sCompileDir) ? PH7_PATH_CACHE . static::COMPILE_DIR . PH7_DS : $this->sCompileDir;

        return $this;
    }

    /**
     * Checks if the cache directory has been defined otherwise we create a default directory.
     * If the folder cache does not exist, it creates a folder.
     *
     * @return self
     */
    private function checkCacheDir()
    {
        $this->sCacheDir = empty($this->sCacheDir) ? PH7_PATH_CACHE . static::CACHE_DIR . PH7_DS : $this->sCacheDir;

        return $this;
    }

    /**
     * @param string $sKeyword
     *
     * @return bool
     */
    private function isKeywordFoundInCode($sKeyword)
    {
        return strpos($this->sCode, $sKeyword) !== false;
    }

    /**
     * Set the error message.
     *
     * @return void
     */
    final private function setErrMsg()
    {
        $this->sCode = sprintf(static::ERR_MSG, self::SOFTWARE_EMAIL);
    }

    public function __destruct()
    {
        $this->clean();
    }
}
