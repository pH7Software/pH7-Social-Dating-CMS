<?php
/**
 * Uri Router for URLs rewrite.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mvc / Router
 */

namespace PH7\Framework\Mvc\Router;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Pattern\Statik, PH7\Framework\Parse\Url;

class Uri
{
    /**
     * @staticvar boolean $_bFullClean If you need to completely clean URL.
     */
    private static $_bFullClean;

    /**
     * Import the trait to set the class static.
     * The trait sets constructor/clone private to prevent instantiation.
     */
    use Statik;

    /**
     * Load route file.
     *
     * @param string \DOMDocument $oDom
     * @return object \DOMDocument
     * @throws \PH7\Framework\File\Exception If the file is not found.
     */
    public static function loadFile(\DOMDocument $oDom)
    {
        $sPathLangName = PH7_PATH_APP_CONFIG . 'routes/' . PH7_LANG_CODE . '.xml';
        $sPathDefaultLang = PH7_PATH_APP_CONFIG . 'routes/' . PH7_DEFAULT_LANG_CODE . '.xml';

        if (is_file($sPathLangName))
            $sRoutePath = $sPathLangName;
        elseif (is_file($sPathDefaultLang))
            $sRoutePath = $sPathDefaultLang;
        else
            throw new \PH7\Framework\File\Exception('File route xml not found: ' . $sPathDefaultLang);

        $sContents = file_get_contents($sRoutePath); // Get the XML contents
        $sContents = static::_parseVariable($sContents); // Parse the variables
        $oDom->loadXML($sContents); // Load the XML contents

        return $oDom;
    }

    /**
     * @param string $sModule
     * @param string $sController
     * @param string $sAction
     * @param string $sVars Default NULL
     * @param boolean $bFullClean Default TRUE
     * @return string
     */
    public static function get($sModule, $sController, $sAction, $sVars = null, $bFullClean = true)
    {
        static::$_bFullClean = $bFullClean;
        $sUrl = static::_uri( array('module' => $sModule, 'controller' => $sController, 'action' => $sAction, 'vars' => $sVars) );
        return $sUrl;
    }

    /**
     * @access private
     * @param array $aParams
     * @return string
     * @throws \PH7\Framework\File\Exception If the XML file is not found.
     */
    private static function _uri(array $aParams)
    {
        $sModule = $aParams['module'];
        $sController = $aParams['controller'];
        $sAction = $aParams['action'];
        $sVars = ''; // Default value

        if (!empty($aParams['vars']))
        {
            // Omit the commas which may be part of a sentence in the URL parameters
            $aParams['vars'] = str_replace(array(', ', ' ,'), '', $aParams['vars']);

            $aVars = explode(',', $aParams['vars']);
            foreach ($aVars as $sVar)
                $sVars .= PH7_SH . $sVar;
            unset($aVars);

            $sVars = Url::clean($sVars, static::$_bFullClean);

        }

        $oUrl = static::loadFile(new \DOMDocument);
        foreach ($oUrl->getElementsByTagName('route') as $oRoute)
        {
            if (preg_match('#^' . $oRoute->getAttribute('module') . '$#', $sModule) && preg_match('#^' . $oRoute->getAttribute('controller') . '$#', $sController) && preg_match('#^' . $oRoute->getAttribute('action') . '$#', $sAction))
            {
                // Strip the special characters
                $sUri = $oRoute->getAttribute('url');
                $sUri = str_replace('\\', '', $sUri);
                $sUri = preg_replace('#\(.+\)#', '', $sUri);
                $sUri = preg_replace('#([/\?]+)$#', '',$sUri);
                return PH7_URL_ROOT . $sUri . $sVars;
            }
        }
        unset($oUrl);

        return PH7_URL_ROOT . "$sModule/$sController/$sAction$sVars";
    }

    /**
     * Parse the variables route.
     *
     * @access private
     * @param string $sContents
     * @return string The contents parsed.
     */
    private static function _parseVariable($sContents)
    {
        /**
         * Replace the "[$page_ext]" variable by the "PH7_PAGE_EXT" constant.
         *
         * @internal We add a slash for RegEx ignores the dot (e.g., '.'html), (in RegEx, the dot means "any single character").
         */
        $sContents = str_replace('[$page_ext]', '\\' . PH7_PAGE_EXT, $sContents);

        /**
         * Replace the "[$admin_mod]" variable by the "PH7_ADMIN_MOD" constant.
         */
        $sContents = str_replace('[$admin_mod]', PH7_ADMIN_MOD, $sContents);

        return $sContents;
    }
}
