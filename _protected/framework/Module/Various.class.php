<?php
/**
 * @title            Useful class for managing the system modules.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Module
 */

declare(strict_types=1);

namespace PH7\Framework\Module;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Module as ModuleModel;

class Various
{
    /**
     * @param string $sModFolderName Name of the module folder.
     *
     * @return bool
     */
    public static function isEnabled(string $sModFolderName): bool
    {
        $oMods = (new ModuleModel())->get($sModFolderName);

        // If the module is not in the SysModsEnabled table, return always TRUE
        return !isset($oMods->enabled) || $oMods->enabled === ModuleModel::YES;
    }
}
