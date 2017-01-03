<?php
/**
 * @title            File Permission Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Permission
 */

namespace PH7\Framework\File\Permission;
defined('PH7') or exit('Restricted access');

class File
{

    public function canReadWrite($sFile)
    {
        clearstatcache();
        return (is_file($sFile) && is_readable($sFile) && is_writable($sFile));
    }

    public function canExecute($sFile)
    {
        clearstatcache();
        return (is_file($sFile) && is_executable($sFile));
    }

}

