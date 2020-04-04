<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
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
     * @param int|null $iDefaultMembershipId Specify another value than the default membership ID set. Optional.
     *
     * @return bool
     */
    public static function undeletable($iMembershipId, $iDefaultMembershipId = null)
    {
        if ($iDefaultMembershipId === null) {
            $iDefaultMembershipId = (int)DbConfig::getSetting('defaultMembershipGroupId');
        }

        $aUndeletableGroups = self::UNDELETABLE_GROUP_IDS;
        $aUndeletableGroups[] = $iDefaultMembershipId;
        $iMembershipId = (int)$iMembershipId;

        return in_array($iMembershipId, $aUndeletableGroups, true);
    }
}
