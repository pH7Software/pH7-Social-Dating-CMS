<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\Select;
use PFBC\Element\Token;
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
        $oForm->addElement(new Hidden('submit_notification', 'form_notification'));
        $oForm->addElement(new Token('notification'));
        $oForm->addElement(new Select(t('Newsletters'), 'enable_newsletters', ['1' => t('Enable'), '0' => t('Disable')], ['description' => t('By enabling this option, you would be likely to receive occasional news on our website and our services and offers, promotions and other benefits to our partners.'), 'value' => $oNotification->enableNewsletters, 'required' => 1]));
        $oForm->addElement(new Select(t('Messages'), 'new_msg', ['1' => t('Yes'), '0' => t('No')], ['value' => $oNotification->newMsg, 'required' => 1]));
        $oForm->addElement(new Select(t('Friend requests'), 'friend_request', ['1' => t('Yes'), '0' => t('No')], ['value' => $oNotification->friendRequest, 'required' => 1]));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
