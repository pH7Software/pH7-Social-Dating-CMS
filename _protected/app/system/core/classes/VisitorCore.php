<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use stdClass;

class VisitorCore
{
    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    /** @var ProfileBaseController */
    private $oProfile;

    /** @var CDateTime */
    private $oDateTime;

    public function __construct(ProfileBaseController $oProfile)
    {
        $this->oProfile = $oProfile;
        $this->oDateTime = new CDateTime;
    }

    /**
     * @return void
     */
    public function updateViews()
    {
        $oVisitorModel = new VisitorCoreModel(
            $this->oProfile->getProfileId(),
            $this->oProfile->getVisitorId(),
            $this->oDateTime->get()->dateTime(self::DATETIME_FORMAT)
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

    public function isViewUpdateEligible(stdClass $oPrivacyViewsUser, stdClass $oPrivacyViewsVisitor)
    {
        return $oPrivacyViewsUser->userSaveViews === PrivacyCore::YES &&
            $oPrivacyViewsVisitor->userSaveViews === PrivacyCore::YES &&
            !$this->oProfile->isOwnProfile();
    }
}
