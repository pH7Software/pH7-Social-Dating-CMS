<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File / Permission
 */

namespace PH7\Framework\File\Permission;

final class Chmod
{
    // Same permissions for User, Group, Other
    const MODE_ALL_READ = 0444;
    const MODE_ALL_WRITE = 0666;
    const MODE_ALL_EXEC = 0777;

    // User has different permissions with Group/Other
    const MODE_WRITE_READ = 0644;
    const MODE_EXEC_READ = 0755;
}
