<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2017-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Layout\Html\Meta;

class MainController extends Controller
{
    public const MAX_PROFILES = 5;

    /*
     * The SQL pool is fetched in the model's default order (recent activity), then re-ranked by
     * compatibility. Fetching several times more candidates than we display gives the scorer real
     * headroom, so a highly-compatible match still surfaces even when it isn't the most recently active.
     */
    public const CANDIDATE_POOL_MULTIPLIER = 12;
    public const MAX_CANDIDATES = self::MAX_PROFILES * self::CANDIDATE_POOL_MULTIPLIER;

    /** @var UserCoreModel */
    private $oUserModel;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel();
    }

    public function index($iProfileId = null)
    {
        $this->view->header = Meta::NOINDEX;

        if (!empty($iProfileId)) {
            $oProfileData = $this->oUserModel->readProfile($iProfileId);
            $oProfileFields = $this->oUserModel->getInfoFields($iProfileId);
            $oRelatedProfiles = $this->relatedProfiles($oProfileData, $oProfileFields);

            if (!empty($oRelatedProfiles)) {
                $this->view->avatarDesign = new AvatarDesignCore(); // Avatar Design Class
                $this->view->related_profiles = $oRelatedProfiles;
                $this->view->id = $iProfileId;
            }
        }

        $this->output();
    }

    /**
     * Get related profile data.
     *
     * @param \stdClass $oProfile       user data
     * @param \stdClass $oProfileFields profile fields
     *
     * @return array|int related profiles
     */
    private function relatedProfiles(\stdClass $oProfile, \stdClass $oProfileFields)
    {
        $aParams = [
            SearchQueryCore::AGE => $oProfile->birthDate,
            SearchQueryCore::MATCH_SEX => $oProfile->matchSex,
            SearchQueryCore::COUNTRY => $oProfileFields->country,
            SearchQueryCore::CITY => $oProfileFields->city
        ];

        $aCandidates = $this->oUserModel->search($aParams, false, 0, self::MAX_CANDIDATES);
        if (!is_array($aCandidates)) {
            return $aCandidates;
        }

        // Give the profile's location to the scorer (profile fields live in a separate table)
        $oProfile->city = $oProfileFields->city ?? '';
        $oProfile->country = $oProfileFields->country ?? '';

        return MatchmakingCore::rank($oProfile, $aCandidates, self::MAX_PROFILES);
    }
}
