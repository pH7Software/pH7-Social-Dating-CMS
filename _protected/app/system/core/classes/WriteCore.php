<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

abstract class WriteCore
{
    const THUMBNAIL_FILENAME = 'thumb.png';

    /**
     * @param mixed (integer | string) $mId Put the username + the PH7_DS constant + the image file for the Note module or just the post ID for the Blog module.
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     * @param \PH7\Framework\File\File $oFile
     * @return boolean
     */
    public function deleteThumb($mId, $sMod, Framework\File\File $oFile)
    {
        self::checkMod($sMod);

        if ($sMod === 'blog') {
            $mId .= PH7_DS . static::THUMBNAIL_FILENAME;
        }

        return $oFile->deleteDir(PH7_PATH_PUBLIC_DATA_SYS_MOD . $sMod . PH7_DS . PH7_IMG . $mId);
    }

    /**
     * @param string $sMod Module name. Choose between 'blog' and 'note'.
     * @return void
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException If the module is incorrect.
     */
    public static function checkMod($sMod)
    {
        if ($sMod !== 'blog' && $sMod !== 'note') {
            Framework\Error\CException\PH7InvalidArgumentException('Bad module: ' . $sMod);
        }
    }
}
