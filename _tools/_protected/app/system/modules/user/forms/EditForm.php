<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\Date;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Radio;
use PFBC\Element\Select;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\BirthDate;
use PFBC\Validation\Name;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header;

class EditForm
{
    public static function display()
    {
        $oHttpRequest = new HttpRequest;
        $iProfileId = self::getProfileId($oHttpRequest);

        if (isset($_POST['submit_user_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_user_edit_account'])) {
                new EditFormProcess($iProfileId);
            }
            Header::redirect();
        }

        $oUserModel = new UserModel;
        $oUser = $oUserModel->readProfile($iProfileId);

        // Birth Date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oUser->birthDate)->date('Y-m-d');

        $oForm = new \PFBC\Form('form_user_edit_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_user_edit_account', 'form_user_edit_account'));
        $oForm->addElement(new Token('edit_account'));

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            $oForm->addElement(
                new HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'user', 'browse') . '">' . t('Back to Browse Users') . '</a></p>')
            );

            $oMemberships = (new AdminCoreModel)->getMemberships();
            $aGroupName = [];
            foreach ($oMemberships as $oGroup) {
                // Retrieve only the activated memberships
                if ($oGroup->enable == 1) {
                    $aGroupName[$oGroup->groupId] = $oGroup->name;
                }
            }
            $oForm->addElement(new Select(t('Membership Group:'), 'group_id', $aGroupName, ['value' => $oUser->groupId, 'required' => 1]));
            unset($aGroupName);
        }
        unset($oHR);

        $oForm->addElement(new Textbox(t('First Name:'), 'first_name', ['id' => 'name_first', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oUser->firstName, 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_first"></span>'));

        $oForm->addElement(new Textbox(t('Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oUser->lastName, 'validation' => new Name]));
        $oForm->addElement(new HTMLExternal('<span class="input_error name_last"></span>'));

        $oForm->addElement(new Textbox(t('Nickname:'), 'username', ['description' => t('For security reasons, you cannot change your nickname.'), 'disabled' => 'disabled', 'value' => $oUser->username]));

        $oForm->addElement(new Email(t('Email:'), 'mail', ['description' => t('For security reasons and to avoid spam, you cannot change your email address. If it has changed, you will need to <a href="%0%">delete</a> your account and create a new one.', Uri::get('user', 'setting', 'delete')), 'disabled' => 'disabled', 'value' => $oUser->email]));

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            // For security reasons, only admins can change profile gender
            $oForm->addElement(
                new Radio(
                    t('Gender:'),
                    'sex',
                    [
                        GenderTypeUserCore::FEMALE => t('Woman'),
                        GenderTypeUserCore::MALE => t('Man'),
                        GenderTypeUserCore::COUPLE => t('Couple')
                    ],
                    [
                        'value' => $oUser->sex,
                        'required' => 1
                    ]
                )
            );
        }

        $oForm->addElement(
            new Checkbox(
                t('Looking for a:'),
                'match_sex',
                [
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::FEMALE => t('Woman'),
                    GenderTypeUserCore::COUPLE => t('Couple')
                ],
                ['value' => Form::getVal($oUser->matchSex), 'required' => 1]
            )
        );

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            // For security reasons, only admins can change the date of birth
            $oForm->addElement(
                new Date(
                    t('Date of birth:'),
                    'birth_date',
                    [
                        'id' => 'birth_date',
                        'onblur' => 'CValid(this.value, this.id)',
                        'value' => $sBirthDate,
                        'validation' => new BirthDate,
                        'required' => 1
                    ]
                )
            );
            $oForm->addElement(new HTMLExternal('<span class="input_error birth_date"></span>'));
        }

        // Generate dynamic fields
        $oFields = $oUserModel->getInfoFields($iProfileId);
        foreach ($oFields as $sColumn => $sValue) {
            $oForm = (new DynamicFieldCoreForm($oForm, $sColumn, $sValue))->generate();
        }

        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script><script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return int
     */
    private static function getProfileId(HttpRequest $oHttpRequest)
    {
        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            return $oHttpRequest->get('profile_id', 'int');
        }

        return (new Session)->get('member_id');
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return bool
     */
    private static function isAdminLoggedAndUserIdExists(HttpRequest $oHttpRequest)
    {
        return AdminCore::auth() && !User::auth() &&
            $oHttpRequest->getExists('profile_id');
    }
}
