<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

class NotificationFormProcess extends Form
{

    /**
     * @param integer $iProfileId
     * @param \PH7\UserCoreModel $oUserModel
     * @return void
     */
    public function __construct($iProfileId, UserCoreModel $oUserModel)
    {
         parent::__construct();

         $oGetNotofication = $oUserModel->getNotification($iProfileId);

        if(!$this->str->equals($this->httpRequest->post('enable_newsletters'), $oGetNotofication->enableNewsletters))
            $oUserModel->setNotification('enableNewsletters', $this->httpRequest->post('enable_newsletters'), $iProfileId);

        if(!$this->str->equals($this->httpRequest->post('new_msg'), $oGetNotofication->newMsg))
            $oUserModel->setNotification('newMsg', $this->httpRequest->post('new_msg'), $iProfileId);

        if(!$this->str->equals($this->httpRequest->post('friend_request'), $oGetNotofication->friendRequest))
            $oUserModel->setNotification('friendRequest', $this->httpRequest->post('friend_request'), $iProfileId);

        unset($oUserModel);

        /* Clean UserCoreModel Cache */
        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'notification' . $iProfileId, null)->clear()
        ->start(UserCoreModel::CACHE_GROUP, 'isNotification' . $iProfileId, null)->clear();

        \PFBC\Form::setSuccess('form_notification', t('Your notifications settings have been saved successfully!'));
    }

}
