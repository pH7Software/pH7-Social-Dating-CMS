<?php
/**
 * @title            Permission Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Permission
 */

namespace PH7\Framework\File\Permission;

defined('PH7') or exit('Restricted access');

class Permission
{
    const READABLE_WRITABLE = 6;
    const EXECUTABLE = 1;
    const WRITABLE_EXECUTABLE = 3;

    /**
     * Check the file permissions.
     *
     * @param string $sFile File name.
     * @param int $iMode Permission that the file should have.
     *
     * @return bool
     */
    public function checkFileAccess($sFile, $iMode)
    {
        $oFile = new File;
        $bRet = false; // Default value

        if ($iMode === static::READABLE_WRITABLE) {
            $bRet = $oFile->canReadWrite($sFile);
        } elseif ($iMode === static::EXECUTABLE) {
            $bRet = $oFile->canExecute($sFile);
        }

        unset($oFile);

        return $bRet;
    }

    /**
     * Check the folder permissions.
     *
     * @param string $sFolder Folder name.
     * @param int $iMode Permission that the folder should have.
     *
     * @return bool
     */
    public function checkFolderAccess($sFolder, $iMode)
    {
        $oFolder = new Folder;
        $bRet = false; // Default value

        if ($iMode === static::READABLE_WRITABLE) {
            $bRet = $oFolder->canReadWrite($sFolder);
        }

        unset($oFolder);

        return $bRet;
    }
}
