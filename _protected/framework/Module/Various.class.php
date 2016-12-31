<?php
/**
 * @title            Useful class for managing the system modules.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2016-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Module
 */

namespace PH7\Framework\Module;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Module;

class Various
{

    /**
     * @param string $sModFolderName Name of the module folder.
     * @return boolean
     */
    public static function isEnabled($sModFolderName)
    {
        $oMods = (new Module)->get($sModFolderName);

        // If the module is not in the SysModsEnabled table, return always TRUE
        if (!isset($oMods->enabled)) return true;

        return (((int)$oMods->enabled) === 1);
    }
}
