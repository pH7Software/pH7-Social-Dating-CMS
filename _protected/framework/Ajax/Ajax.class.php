<?php

/**
 * @title            Ajax Helper Class
 *
 * @author           Pierre-Henry SORIA <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7\Framework\Ajax {
    defined('PH7') or exit('Restricted access');

    class Ajax
    {
        /**
         * @param int $iStatus 1 = success | 0 = error
         *
         * @return string JSON Format
         */
        public static function jsonMsg(int $iStatus, string $sTxt): string
        {
            return json_encode(
                [
                    'status' => $iStatus,
                    'txt' => $sTxt
                ],
                JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
            );
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
