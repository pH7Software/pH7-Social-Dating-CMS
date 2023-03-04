<?php
/**
 * @title            General Class
 * @desc             Useful methods for the code structure.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Structure
 */

namespace PH7\Framework\Structure {
    defined('PH7') or exit('Restricted access');

    use PH7\Framework\File\File;

    class General
    {
        /**
         * Emit a signal.
         *
         * @param mixed $mVar [, string $... ]
         *
         * @return string
         */
        public static function emit()
        {
            $aArgs = func_get_args();
            return implode("\t", $aArgs) . File::EOL;
        }

        /**
         * PHP 6 was to give birth to this function, but the development PHP team to decline this feature :-(, so we create this.
         *
         * @param string $sVar a variable (e.g. $_GET['foo'])
         * @param string $sOr a message if $sVar is empty (optional)
         *
         * @return string $sVar or $sOr
         */
        public function ifsetor($sVar, $sOr = '')
        {
            return isset($sVar) ? $sVar : $sOr;
        }
    }
}

namespace {
    use PH7\Framework\Structure\General as GeneralStructure;

    /**
     * Alias for GeneralStructure::ifsetor()
     */
    function ifsetor($sVar, $sOr = '')
    {
        return (new GeneralStructure)->ifsetor($sVar, $sOr);
    }

    /**
     * Alias for GeneralStructure::emit()
     */
    function emit()
    {
        return GeneralStructure::emit();
    }
}
