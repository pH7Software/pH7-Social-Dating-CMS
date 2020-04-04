<?php
/**
 * @title            File Permission Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Permission
 */

namespace PH7\Framework\File\Permission;

defined('PH7') or exit('Restricted access');

class File
{
    /**
     * @param string $sFile
     *
     * @return bool
     */
    public function canReadWrite($sFile)
    {
        clearstatcache();

        return is_file($sFile) && is_readable($sFile) && is_writable($sFile);
    }

    /**
     * @param string $sFile
     *
     * @return bool
     */
    public function canExecute($sFile)
    {
        clearstatcache();

        return is_file($sFile) && is_executable($sFile);
    }
}

