<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;

class NotificationFormProcess extends Form
{
    /**
     * @param int $iProfileId
     * @param UserCoreModel $oUserModel
     */
    public function __construct($iProfileId, UserCoreModel $oUserModel)
    {
        parent::__construct();

        $oGetNotofication = $oUserModel->getNotification($iProfileId);

        if (!$this->str->equals($this->httpRequest->post('enable_newsletters'), $oGetNotofication->enableNewsletters)) {
            $oUserModel->setNotification('enableNewsletters', $this->httpRequest->post('enable_newsletters'), $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('new_msg'), $oGetNotofication->newMsg)) {
            $oUserModel->setNotification('newMsg', $this->httpRequest->post('new_msg'), $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('friend_request'), $oGetNotofication->friendRequest)) {
            $oUserModel->setNotification('friendRequest', $this->httpRequest->post('friend_request'), $iProfileId);
        }

        $this->clearCache($iProfileId);

        \PFBC\Form::setSuccess('form_notification', t('Your notifications settings have been saved successfully!'));
    }

    /**
     * @param int $iProfileId
     */
    private function clearCache($iProfileId)
    {
        (new Cache)
            ->start(UserCoreModel::CACHE_GROUP, 'notification' . $iProfileId, null)->clear()
            ->start(UserCoreModel::CACHE_GROUP, 'isNotification' . $iProfileId, null)->clear();
    }
}
