<?php
/**
 * @title            Ajax Helper Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Ajax
 */

declare(strict_types=1);

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
        public static function jsonMsg(int $iStatus, string $sTxt): string
        {
            return '{"status":' . $iStatus . ',"txt":"' . $sTxt . '"}';
        }
    }
}

namespace {
    use PH7\Framework\Ajax\Ajax;

    /**
     * Alias of Ajax::jsonMsg() method.
     */
    function jsonMsg(int $iStatus, string $sTxt): string
    {
        return Ajax::jsonMsg($iStatus, $sTxt);
    }
}
