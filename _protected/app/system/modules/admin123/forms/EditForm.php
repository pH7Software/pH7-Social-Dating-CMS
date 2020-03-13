<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Radio;
use PFBC\Element\Textbox;
use PFBC\Element\Timezone;
use PFBC\Element\Token;
use PFBC\Validation\Name;
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

        if (isset($_POST['submit_admin_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_admin_edit_account'])) {
                new EditFormProcess($iProfileId);
            }

            Header::redirect();
        }

        $oAdmin = (new AdminModel)->readProfile($iProfileId, DbTableName::ADMIN);

        $oForm = new \PFBC\Form('form_admin_edit_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_admin_edit_account', 'form_admin_edit_account'));
        $oForm->addElement(new Token('edit_account'));

        if (self::isEditModeEligible($oHttpRequest)) {
            $oForm->addElement(
                new HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get(PH7_ADMIN_MOD, 'admin', 'browse') . '">' . t('Back to Browse Admins') . '</a></p>')
            );
        }

        $oForm->addElement(new Textbox(t('Login Username:'), 'username', ['value' => $oAdmin->username, 'required' => 1]));
        $oForm->addElement(new Email(t('Login Email:'), 'mail', ['value' => $oAdmin->email, 'required' => 1]));
        $oForm->addElement(new Textbox(t('First Name:'), 'first_name', ['value' => $oAdmin->firstName, 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(new Textbox(t('Last Name:'), 'last_name', ['value' => $oAdmin->lastName, 'required' => 1, 'validation' => new Name]));
        $oForm->addElement(
            new Radio(
                t('Gender:'),
                'sex',
                [
                    GenderTypeUserCore::MALE => t('Man'),
                    GenderTypeUserCore::FEMALE => t('Female')
                ],
                [
                    'value' => $oAdmin->sex,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new Timezone('Time Zone:', 'time_zone', ['description' => t('With your time zone, the other administrators may know when they can contact you easily.'), 'value' => $oAdmin->timeZone, 'required' => 1]));
        $oForm->addElement(new Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->render();
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return int
     */
    private static function getProfileId(HttpRequest $oHttpRequest)
    {
        if (self::isEditModeEligible($oHttpRequest)) { // Prohibits other admins to edit Root Admin (ID 1)
            return $oHttpRequest->get('profile_id', 'int');
        }

        return (new Session)->get('admin_id');
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return bool
     */
    private static function isEditModeEligible(HttpRequest $oHttpRequest)
    {
        return $oHttpRequest->getExists('profile_id') &&
            !AdminCore::isRootProfileId($oHttpRequest->get('profile_id', 'int'));
    }
}
