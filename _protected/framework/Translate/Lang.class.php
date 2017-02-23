<?php
/**
 * @title            Lang Class
 * @desc             Loading and management files languages (I18N).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2010-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Translate
 */

namespace PH7\Framework\Translate
{
 defined('PH7') or exit('Restricted access');

 use PH7\Framework\Config\Config, PH7\Framework\Cookie\Cookie;

 class Lang
 {
     const COOKIE_NAME = 'pHSLang';

     private $_oConfig, $_sDefaultLang, $_sUserLang, $_sLangName;

     public function __construct()
     {
         $this->_oConfig = Config::getInstance();
         $oCookie = new Cookie;

         // Check a template name has been entered and if it meets the required length.
         if (!empty($_REQUEST['l']) && strlen($_REQUEST['l']) == 5)
         {
             $this->_sUserLang = $_REQUEST['l'];
             $oCookie->set(static::COOKIE_NAME, $this->_sUserLang, 60*60*48);
         }
         else if ($oCookie->exists(static::COOKIE_NAME))
         {
             $this->_sUserLang = $oCookie->get(static::COOKIE_NAME);
         }
         else
         {
             $this->_sUserLang = (new \PH7\Framework\Navigation\Browser)->getLanguage();
         }

         unset($oCookie);
     }

     /**
      * Set the default language name.
      *
      * @param string $sNewDefLang Prefix of the language.
      * @return object $this
      */
     public function setDefaultLang($sNewDefLang)
     {
         $this->_sDefaultLang = $sNewDefLang;

         return $this;
     }

     /**
      * Set the user language name.
      *
      * @param string $sNewDefaultLang Prefix of the language.
      * @return object $this
      */
     public function setUserLang($sNewUserLang)
     {
         $this->_sUserLang = $sNewUserLang;

         return $this;
     }

     /**
      * Get the default language name.
      *
      * @return string The prefix of the language.
      */
     public function getDefaultLang()
     {
         return $this->_sDefaultLang;
     }

     /**
      * Get the current language name.
      *
      * @return string The prefix of the language.
      */
     public function getLang()
     {
         return $this->_sLangName;
     }

     /**
      * Get JavaScript language file.
      *
      * @static
      * @param string $sPath The path.
      * @param string $sFileName The language name. Default is the constant: 'PH7_LANG_CODE'
      * @return string Valid file name (with the extension).
      * @throws \PH7\Framework\Translate\Exception If the language file is not found.
      */
     public static function getJsFile($sPath, $sFileName = PH7_LANG_CODE)
     {
         if (is_file($sPath . $sFileName . '.js'))
         {
             return $sFileName . '.js';
         }
         else if (is_file($sPath . PH7_DEFAULT_LANG_CODE . '.js'))
         {
             return PH7_DEFAULT_LANG_CODE . '.js';
         }
         else
         {
             throw new Exception('Language file \'' . $sPath . PH7_DEFAULT_LANG_CODE . '.js\' not found.');
         }
     }

     /**
      * Load the language file.
      *
      * @param string $sFileName The language filename (e.g., "global").
      * @param string $sPath If you want to change the default path (the path to the current module), you can specify the path. Default NULL
      * @return object $this
      */
     public function load($sFileName, $sPath = null)
     {
         textdomain($sFileName);
         bindtextdomain($sFileName, (empty($sPath) ? \PH7\Framework\Registry\Registry::getInstance()->path_module_lang : $sPath) );
         bind_textdomain_codeset($sFileName, PH7_ENCODING);

         return $this;
     }

     /**
      * Loading language files.
      *
      * @return object $this
      * @throws \PH7\Framework\Translate\Exception If the language file is not found.
      */
     public function init()
     {
         if (!empty($this->_sUserLang) && $this->_oConfig->load(PH7_PATH_APP_LANG . $this->_sUserLang . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) && is_file( PH7_PATH_APP_LANG . $this->_sUserLang . '/language.php' ))
         {
             $this->_sLangName = $this->_sUserLang;
             include PH7_PATH_APP_LANG . $this->_sUserLang . '/language.php';
             date_default_timezone_set($this->_oConfig->values['language.application']['timezone']);
         }
         else if ($this->_oConfig->load(PH7_PATH_APP_LANG . $this->_sDefaultLang . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) && is_file( PH7_PATH_APP_LANG . $this->_sDefaultLang . '/language.php' ))
         {
             $this->_sLangName = $this->_sDefaultLang;
             include PH7_PATH_APP_LANG . $this->_sDefaultLang . '/language.php';
             date_default_timezone_set($this->_oConfig->values['language.application']['timezone']);
         }
         else if ($this->_oConfig->load(PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE) && is_file( PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . '/language.php' ))
         {
             $this->_sLangName = PH7_DEFAULT_LANG;
             include PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . '/language.php';
             date_default_timezone_set($this->_oConfig->values['language.application']['timezone']);
         }
         else
         {
             throw new Exception('Language file \'' . PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE . '\' and/or Language file \'' . PH7_PATH_APP_LANG . PH7_DEFAULT_LANG . PH7_DS . 'language.php\' not found.');
         }

         // Set the encoding for the specific language set.
         $this->_setEncoding();

         return $this;
     }

    /**
     * Set the correct charset to the site.
     *
     * @return void
     */
    private function _setEncoding()
    {
        if (!defined('PH7_ENCODING'))
            define('PH7_ENCODING', $this->_oConfig->values['language']['charset']);

        mb_internal_encoding(PH7_ENCODING);
        mb_http_output(PH7_ENCODING);
        mb_http_input(PH7_ENCODING);
        mb_language('uni');
        mb_regex_encoding(PH7_ENCODING);
    }
 }

}

namespace
{

 use PH7\Framework\Registry\Registry, PH7\Framework\Parse\SysVar;

 /**
  * Check if GetText PHP extension exists, if not, it'll includes the GetText library.
  */
 if (!function_exists('gettext'))
     require __DIR__ . '/Adapter/Gettext/gettext.inc.php';

 /**
  * Language helper function.
  *
  * @param string $sVar [, string $... ]
  * @return string Returns the text with gettext function or language in an array (this depends on whether a key language was found in the language table).
  */
 function t(...$aTokens)
 {
     $sToken = $aTokens[0];
     $sToken = (Registry::getInstance()->lang !== '' && array_key_exists($sToken, Registry::getInstance()->lang) ? Registry::getInstance()->lang[$sToken] : gettext($sToken));

     for ($i = 1, $iFuncArgs = count($aTokens); $i < $iFuncArgs; $i++) {
         $sToken = str_replace('%'. ($i-1) . '%', $aTokens[$i], $sToken);
     }

     return (new SysVar)->parse($sToken);
 }

 /**
  * Plurial version of t() function.
  *
  * @param string $sMsg1 Singular string.
  * @param string $sMsg2 Plurial string.
  * @param integer $iNumber
  * @return string Returns the text with ngettext function which is the correct plural form of message identified by msgid1 and msgid2 for count n.
  */
 function nt($sMsg1, $sMsg2, $iNumber)
 {
     $sMsg1 = str_replace('%n%', $iNumber, $sMsg1);
     $sMsg2 = str_replace('%n%', $iNumber, $sMsg2);

     return ngettext($sMsg1, $sMsg2, $iNumber);
 }

}
