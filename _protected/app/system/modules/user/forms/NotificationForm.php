<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class NotificationForm
{
    public static function display()
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = (int)(new Session)->get('member_id');

        if (isset($_POST['submit_notification'])) {
            if (\PFBC\Form::isValid($_POST['submit_notification'])) {
                new NotificationFormProcess($iProfileId, $oUserModel);
            }

            Header::redirect();
        }

        $oNotification = $oUserModel->getNotification($iProfileId);

        $oForm = new \PFBC\Form('form_notification');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_notification', 'form_notification'));
        $oForm->addElement(new \PFBC\Element\Token('notification'));
        $oForm->addElement(new \PFBC\Element\Select(t('Newsletters'), 'enable_newsletters', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('By enabling this option, you would be likely to receive occasional news on our website and our services and offers, promotions and other benefits to our partners.'), 'value' => $oNotification->enableNewsletters, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Messages'), 'new_msg', ['1' => t('Yes'), '0' => t('No')], ['value' => $oNotification->newMsg, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Select(t('Friend requests'), 'friend_request', ['1' => t('Yes'), '0' => t('No')], ['value' => $oNotification->friendRequest, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
