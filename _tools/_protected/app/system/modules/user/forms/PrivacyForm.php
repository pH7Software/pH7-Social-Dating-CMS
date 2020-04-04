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
use PFBC\Element\HTMLExternal;
use PFBC\Element\Radio;
use PFBC\Element\Select;
use PFBC\Element\Token;
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
        $oForm->addElement(new Hidden('submit_privacy_account', 'form_privacy_account'));
        $oForm->addElement(new Token('privacy_account'));
        $oForm->addElement(new HTMLExternal('<h3><u>' . t('Profile:') . '</u></h3>'));
        $oForm->addElement(
            new Radio(
                t('Who can view your profile?'),
                'privacy_profile',
                [
                    PrivacyCore::ALL => t('Everyone (including people who are not %0% members).', Registry::getInstance()->site_name),
                    PrivacyCore::ONLY_USERS => t('Only %0% members who are logged in.', Registry::getInstance()->site_name),
                    PrivacyCore::ONLY_ME => t('Only me.')
                ],
                [
                    'value' => $oPrivacy->privacyProfile,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<h3><u>' . t('Web search engine:') . '</u></h3>'));
        $oForm->addElement(
            new Radio(
                t('Do you want to be included in search results?'),
                'search_profile',
                [
                    PrivacyCore::YES => t("Yes, include my profile in search results (%site_name%'s search, Google, Bing, Yahoo, etc.)."),
                    PrivacyCore::NO => t('No, do not include my profile in search results.')
                ],
                [
                    'value' => $oPrivacy->searchProfile,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<h3><u>' . t('Show profile visitors:') . '</u></h3>'));
        $oForm->addElement(
            new Radio(
                t('Would you like to display members that have viewed your profile?'),
                'user_save_views',
                [
                    PrivacyCore::YES => t('Yes, display members who viewed my profile (Selecting this option will allow other members to see that you visited their profile).'),
                    PrivacyCore::NO => t('No, don\'t display members who viewed my profile. (Selecting this option will prevent you from seeing who visited your profile).')
                ],
                [
                    'value' => $oPrivacy->userSaveViews,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<h3><u>' . t('Presence:') . '</u></h3>'));
        $oForm->addElement(
            new Select(
                t('Your status <div class="user_status right" id="status_div"></div>'),
                'user_status',
                [
                    UserModel::ONLINE_STATUS => t('Online'),
                    UserModel::BUSY_STATUS => t('Busy'),
                    UserModel::AWAY_STATUS => t('Away'),
                    UserModel::OFFLINE_STATUS => t('Offline')
                ],
                [
                    'id' => 'status',
                    'onchange' => 'init_status()',
                    'value' => $oUserModel->getUserStatus($iProfileId),
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HTMLExternal('<script>$(function(){ init_status() });</script>'));
        $oForm->addElement(new Button);
        $oForm->render();
    }
}
