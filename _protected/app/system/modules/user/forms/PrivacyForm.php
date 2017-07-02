<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */
namespace PH7;

use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;

class PrivacyForm
{

    public static function display()
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = (int) (new Session)->get('member_id');

        if (isset($_POST['submit_privacy_account']))
        {
            if (\PFBC\Form::isValid($_POST['submit_privacy_account']))
                new PrivacyFormProcess($iProfileId, $oUserModel);

            Framework\Url\Header::redirect();
        }

        $oPrivacy = $oUserModel->getPrivacySetting($iProfileId);

        $oForm = new \PFBC\Form('form_privacy_account');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_privacy_account', 'form_privacy_account'));
        $oForm->addElement(new \PFBC\Element\Token('privacy_account'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Profile:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Who can view your profile?'), 'privacy_profile', array('all' => t('Everyone (including people who are not %0% members).', Registry::getInstance()->site_name), 'only_members' => t('Only %0% members who are logged in.', Registry::getInstance()->site_name), 'only_me' => t('Only me.')), array('value' => $oPrivacy->privacyProfile, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Web search engine:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Do you want to be included in search results?'), 'search_profile', array('yes' => t("Yes, include my profile in search results (%site_name%'s search, Google, Bing, Yahoo, etc.)."), 'no' => t('No, do not include my profile in search results.')), array('value' => $oPrivacy->searchProfile, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Show profile visitors:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Would you like to display members that have viewed your profile?'), 'user_save_views', array('yes' => t('Yes, display members who viewed my profile (Selecting this option will allow other members to see that you visited their profile).'), 'no' => t('No, don\'t display members who viewed my profile. (Selecting this option will prevent you from seeing who visited your profile).')), array('value' => $oPrivacy->userSaveViews, 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Presence:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Your status'), 'user_status', array('1' => t('Online'), '2' => t('Busy'), '3' => t('Away'), '0' => 'Offline'), array('id' => 'status', 'onchange' => 'init_status()', 'value' => $oUserModel->getUserStatus($iProfileId), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="user_status right" id="status_div"></div>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script>$(function(){ init_status() });</script>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }

}
