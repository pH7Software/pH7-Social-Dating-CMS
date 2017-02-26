<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Related Profile / Controller
 */
namespace PH7;


use PH7\Framework\Layout\Html\Meta;
use stdClass;

class MainController extends Controller
{
    const MAX_PROFILE = 5;

    private $oUserModel;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
    }

    public function index($iProfileId = null)
    {
        $this->view->header = Meta::NOINDEX;

        if (!empty($iProfileId)) {
            $oProfileData = $this->oUserModel->readProfile($iProfileId);
            $oProfileFields = $this->oUserModel->getInfoFields($iProfileId);
            $oRelatedProfiles = $this->relatedProdiles($oProfileData, $oProfileFields);

            if (!empty($oRelatedProfiles)) {
                $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
                $this->view->related_profiles = $oRelatedProfiles;
                $this->view->id = $iProfileId;
                $this->output();
                return true;
            }
        }
        $this->output();
    }

    /**
     * Get related profile data.
     *
     * @param object $oProfile Profile User Data.
     * @return stdClass Profile data.
     */
    private function relatedProdiles(stdClass $oProfile, stdClass $oProfileFields)
    {
        $aParams = [
            SearchQueryCore::AGE => $oProfile->birthDate,
            SearchQueryCore::MATCH_SEX => $oProfile->matchSex,
            SearchQueryCore::COUNTRY => $oProfileFields->country,
            SearchQueryCore::CITY => $oProfileFields->city
        ];

        return $this->oUserModel->search($aParams, false, 0, self::MAX_PROFILE);
        $this->output();
    }
}
