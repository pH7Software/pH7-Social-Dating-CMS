<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Model;
use PH7\Framework\Mvc\Model\Engine\Util\Various;

class UpgradeCoreModel extends Model
{
    /**
     * Executes sql queries for the upgrade of the software.
     *
     * @param string $sSqlUpgradeFile File SQL
     *
     * @return bool|array Returns TRUE if there are no errors, otherwise returns an ARRAY of error information.
     */
    public function run(string $sSqlUpgradeFile): bool|array
    {
        return Various::execQueryFile($sSqlUpgradeFile);
    }
}
