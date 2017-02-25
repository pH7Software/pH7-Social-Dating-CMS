<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Related Profile / Controller
 */
namespace PH7;

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
        if (!empty($iProfileId)) {
            $oProfileData = $this->oUserModel->readProfile($iProfileId);
            $oRelatedProfiles = $this->relatedProdiles($oProfileData);

            if (!empty($oRelatedProfiles)) {
                $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
                $this->view->related_profiles = $oRelatedProfiles;
                $this->output();
                return true;
            }
        }
        $this->notFound();
    }

    protected function notFound()
    {
        $this->view->error = t('There are no similar profiles.');
    }

    /**
     * @param object $oProfile Profile User Data.
     * @return stdClass Profile data.
     */
    private function relatedProdiles(stdClass $oProfile)
    {
        $aParams = [
            $aParams[SearchQueryCore::AGE] => $oProfile->birthDate,
            SearchQueryCore::COUNTRY => $oProfile->country,
            SearchQueryCore::CITY => $oProfile->city,
            SearchQueryCore::MATCH_SEX => $oProfile->match_sex
        ];

        return $this->oUserModel->search($aParams, false, 0, self::MAX_PROFILE);
        $this->output();
    }
}
