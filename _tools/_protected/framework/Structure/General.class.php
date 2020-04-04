<?php
/**
 * @title            General Class
 * @desc             Useful methods for the code structure.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Structure
 */

namespace PH7\Framework\Structure {
    defined('PH7') or exit('Restricted access');

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
            return implode("\t", $aArgs) . \PH7\Framework\File\File::EOL;
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
    /**
     * Alias for \PH7\Framework\Structure\General::ifsetor()
     */
    function ifsetor($sVar, $sOr = '')
    {
        return (new PH7\Framework\Structure\General)->ifsetor($sVar, $sOr);
    }

    /**
     * Alias for \PH7\Framework\Structure\General::emit()
     */
    function emit()
    {
        return PH7\Framework\Structure\General::emit();
    }
}
