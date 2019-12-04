<?php
/**
 * @title            Ajax Helper Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ajax
 */

namespace PH7\Framework\Ajax {
    defined('PH7') or exit('Restricted access');

    class Ajax
    {
        /**
         * @param int $iStatus 1 = success | 0 = error
         * @param string $sTxt
         *
         * @return string JSON Format
         */
        public static function jsonMsg($iStatus, $sTxt)
        {
            return '{"status":' . $iStatus . ',"txt":"' . $sTxt . '"}';
        }
    }
}

namespace {
    use PH7\Framework\Ajax\Ajax;

    /**
     * Alias of Ajax::jsonMsg() method.
     *
     * @param int $iStatus
     * @param string $sTxt
     *
     * @return string
     */
    function jsonMsg($iStatus, $sTxt)
    {
        return Ajax::jsonMsg($iStatus, $sTxt);
    }
}
