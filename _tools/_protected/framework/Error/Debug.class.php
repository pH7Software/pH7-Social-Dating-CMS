<?php
/**
 * @title          Debug Class
 * @desc           Management debug mode site.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7/ Framework / Error
 * @version        1.1
 */

namespace PH7\Framework\Error {
    defined('PH7') or exit('Restricted access');

    use Exception;
    use PH7\Framework\Config\Config;
    use PH7\Framework\Server\Environment;

    final class Debug
    {
        /**
         * Private constructor to prevent instantiation of class since it's a static class.
         */
        private function __construct()
        {
        }

        /**
         * Gets Information (message, code, file, line, trace) of an Exception.
         *
         * @param Exception $oE
         *
         * @return string
         */
        public static function getInfoExcept(Exception $oE)
        {
            $sDebug = $oE->getMessage();
            $sDebug .= '<br />';
            $sDebug .= $oE->getCode();
            $sDebug .= '<br />';
            $sDebug .= $oE->getFile();
            $sDebug .= '<br />';
            $sDebug .= $oE->getLine();
            $sDebug .= '<br />';
            $sDebug .= $oE->getTraceAsString();

            return $sDebug;
        }

        /**
         * Checks if the CMS is in development mode.
         *
         * @return bool Returns true if the development mode is enabled else returns false.
         */
        public static function is()
        {
            return Config::getInstance()->values['mode']['environment'] === Environment::DEVELOPMENT_MODE;
        }

        /**
         * Clone is set to private to stop cloning.
         */
        private function __clone()
        {
        }
    }
}

namespace {
    use PH7\Framework\Error\Debug;

    /**
     * Alias for Debug::is()
     */
    function isDebug()
    {
        return Debug::is();
    }
}
