<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;

class GroupId
{
    const UNDELETABLE_GROUP_IDS = [
        UserCoreModel::VISITOR_GROUP,
        UserCoreModel::PENDING_GROUP
    ];

    /**
     * Checks if a membership group can be deleted or not.
     *
     * @param int $iMembershipId
     *
     * @return bool
     */
    public static function undeletable($iMembershipId)
    {
        $aUndeletableGroups = self::UNDELETABLE_GROUP_IDS;
        $aUndeletableGroups[] = (int)DbConfig::getSetting('defaultMembershipGroupId');
        $iMembershipId = (int)$iMembershipId;

        return in_array($iMembershipId, $aUndeletableGroups, true);
    }
}
