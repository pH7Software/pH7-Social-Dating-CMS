<?php
/**
 * @title            Ajax Helper Class
 *
 * @author           Pierre-Henry SORIA <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Ajax
 * @version          0.5
 */

namespace PH7\Framework\Ajax {
defined('PH7') or exit('Restricted access');

 class Ajax
 {

     /**
      * @param integer $iStatus, 1 = success, 0 = error
      * @param string $sTxt
      * @return string JSON Format
      */
     public static function jsonMsg($iStatus, $sTxt)
     {
         return '{"status":' . $iStatus . ',"txt":"' . $sTxt . '"}';
     }

 }

}

namespace {

    /**
     * Alias of the \PH7\Framework\Ajax\Ajax::jsonMsg() method.
     */
    function jsonMsg($iStatus, $sTxt)
    {
        return PH7\Framework\Ajax\Ajax::jsonMsg($iStatus, $sTxt);
    }

}
