<?php
/**
 * @title          Debug Class
 * @desc           Management debug mode site.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error
 * @version        1.1
 */

namespace PH7\Framework\Error {
defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;

 final class Debug
 {

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Gets Information (message, code, file, line, trace) of an Exception.
     *
     * @param object $oE Exception object.
     * @return string
     */
    public static function getInfoExcept($oE)
    {
        $sDebug = $oE->getMessage();
        $sDebug.= '<br />';
        $sDebug = $oE->getCode();
        $sDebug.= '<br />';
        $sDebug = $oE->getFile();
        $sDebug.= '<br />';
        $sDebug = $oE->getLine();
        $sDebug.= '<br />';
        $sDebug.= $oE->getTraceAsString();

        return $sDebug;
    }

    /**
     * Checks if the CMS is in development mode.
     *
     * @return boolean Returns true if the development mode is enabled else returns false.
     */
    public static function is()
    {
        return (Config::getInstance()->values['application']['environment'] === 'development');
    }

    /**
     * Clone is set to private to stop cloning.
     *
     * @access private
     */
    private function __clone() {}

 }

}

namespace {

 /**
  * Alias for \PH7\Framework\Error\Debug::is()
  */
 function isDebug()
 {
     return PH7\Framework\Error\Debug::is();
 }

}
