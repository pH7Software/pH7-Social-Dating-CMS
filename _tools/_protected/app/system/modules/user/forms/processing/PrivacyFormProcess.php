<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Cache\Cache;

class PrivacyFormProcess extends Form
{
    /**
     * @param int $iProfileId
     * @param UserCoreModel $oUserModel
     */
    public function __construct($iProfileId, UserCoreModel $oUserModel)
    {
        parent::__construct();

        $oGetPrivacy = $oUserModel->getPrivacySetting($iProfileId);

        if (!$this->str->equals($this->httpRequest->post('privacy_profile'), $oGetPrivacy->privacyProfile)) {
            $oUserModel->updatePrivacySetting('privacyProfile', $this->httpRequest->post('privacy_profile'), $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('search_profile'), $oGetPrivacy->searchProfile)) {
            $oUserModel->updatePrivacySetting('searchProfile', $this->httpRequest->post('search_profile'), $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('user_save_views'), $oGetPrivacy->userSaveViews)) {
            $oUserModel->updatePrivacySetting('userSaveViews', $this->httpRequest->post('user_save_views'), $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('user_status'), $oUserModel->getUserStatus($iProfileId))) {
            $oUserModel->setUserStatus($iProfileId, $this->httpRequest->post('user_status'));
        }

        $this->clearCache($iProfileId);

        \PFBC\Form::setSuccess('form_privacy_account', t('Your privacy settings have been saved successfully!'));
    }

    /**
     * @param int $iProfileId
     */
    private function clearCache($iProfileId)
    {
        (new Cache)
            ->start(UserCoreModel::CACHE_GROUP, 'privacySetting' . $iProfileId, null)->clear()
            ->start(UserCoreModel::CACHE_GROUP, 'userStatus' . $iProfileId, null)->clear();
    }
}
