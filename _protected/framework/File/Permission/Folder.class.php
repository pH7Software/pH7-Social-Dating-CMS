<?php
/**
 * @title            Folder Permission Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / File / Permission
 */

namespace PH7\Framework\File\Permission;

defined('PH7') or exit('Restricted access');

class Folder
{
    /**
     * @param string $sFolder
     *
     * @return bool
     */
    public function canReadWrite($sFolder)
    {
        clearstatcache();

        return is_folder($sFolder) && is_readable($sFolder) && is_writable($sFolder);
    }
}
