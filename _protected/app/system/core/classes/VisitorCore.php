<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use stdClass;

class VisitorCore
{
    /**
     * @return void
     */
    public function updateVisitorViews()
    {
        $oVisitorModel = new VisitorCoreModel(
            $this->iProfileId,
            $this->iVisitorId,
            $this->dateTime->get()->dateTime('Y-m-d H:i:s')
        );

        if (!$oVisitorModel->already()) {
            // Add a new visit
            $oVisitorModel->set();
        } else {
            // Update the date of last visit
            $oVisitorModel->update();
        }
        unset($oVisitorModel);
    }

    public function isVisitorViewUpdateEligible(
        stdClass $oPrivacyViewsUser,
        stdClass $oPrivacyViewsVisitor,
        ProfileBaseController $oProfile
    )
    {
        return $oPrivacyViewsUser->userSaveViews === PrivacyCore::YES &&
            $oPrivacyViewsVisitor->userSaveViews === PrivacyCore::YES &&
            !$oProfile->isOwnProfile();
    }
}
