<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / File
 */

namespace PH7\Framework\File\Permission;

final class Chmod
{
    const MODE_ALL_READ = 0444;
    const MODE_ALL_WRITE = 0666;
    const MODE_READ_WRITE = 0644;
    const MODE_ALL_EXEC = 0777;
}
