<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Registry\Registry;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class PrivacyForm
{
    public static function display()
    {
        $oUserModel = new UserCoreModel;
        $iProfileId = (int)(new Session)->get('member_id');

        if (isset($_POST['submit_privacy_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_privacy_account'])) {
                new PrivacyFormProcess($iProfileId, $oUserModel);
            }

            Header::redirect();
        }

        $oPrivacy = $oUserModel->getPrivacySetting($iProfileId);

        $oForm = new \PFBC\Form('form_privacy_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_privacy_account', 'form_privacy_account'));
        $oForm->addElement(new \PFBC\Element\Token('privacy_account'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Profile:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Who can view your profile?'), 'privacy_profile', ['all' => t('Everyone (including people who are not %0% members).', Registry::getInstance()->site_name), 'only_members' => t('Only %0% members who are logged in.', Registry::getInstance()->site_name), 'only_me' => t('Only me.')], ['value' => $oPrivacy->privacyProfile, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Web search engine:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Do you want to be included in search results?'), 'search_profile', ['yes' => t("Yes, include my profile in search results (%site_name%'s search, Google, Bing, Yahoo, etc.)."), 'no' => t('No, do not include my profile in search results.')], ['value' => $oPrivacy->searchProfile, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Show profile visitors:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Radio(t('Would you like to display members that have viewed your profile?'), 'user_save_views', ['yes' => t('Yes, display members who viewed my profile (Selecting this option will allow other members to see that you visited their profile).'), 'no' => t('No, don\'t display members who viewed my profile. (Selecting this option will prevent you from seeing who visited your profile).')], ['value' => $oPrivacy->userSaveViews, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3><u>' . t('Presence:') . '</u></h3>'));
        $oForm->addElement(new \PFBC\Element\Select(t('Your status <div class="user_status right" id="status_div"></div>'), 'user_status', [UserModel::ONLINE_STATUS => t('Online'), UserModel::BUSY_STATUS => t('Busy'), UserModel::AWAY_STATUS => t('Away'), UserModel::OFFLINE_STATUS => 'Offline'], ['id' => 'status', 'onchange' => 'init_status()', 'value' => $oUserModel->getUserStatus($iProfileId), 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script>$(function(){ init_status() });</script>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->render();
    }
}
