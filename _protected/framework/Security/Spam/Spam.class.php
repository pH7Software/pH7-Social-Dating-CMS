<?php
/**
 * @title          Spam Class
 * @desc           To prevent spam.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Security / Spam
 * @version        0.1
 */

namespace PH7\Framework\Security\Spam;
defined('PH7') or exit('Restricted access');

class Spam
{

    /**
     * Detect duplicate contents. Processing strings case-insensitive.
     *
     * @param string $sText1
     * @param string $sText2
     * @return boolean Returns TRUE if similar content was found in the table, FALSE otherwise.
     */
    public static function detectDuplicate($sText1, $sText2)
    {
        $aErase = array('#', '@', '&nbsp;', '-', '_', '|', ';', '.', ',', '!', '?', '&', "'", '"', '(', ')', '<p>', '</p>', '<span>', '</span>', '<div>', '</div>', '<br', '<', '>', "\n", "\r", "\t", " ");

        $sText1 = str_ireplace($aErase, '', $sText1);
        $sText2 = str_ireplace($aErase, '', $sText2);

        return (false !== stripos($sText1, $sText2));
    }

}
