<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

namespace PH7;

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

        if (isset($_POST['submit_aff_edit_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_aff_edit_account'])) {
                new EditFormProcess($iProfileId);
            }
            Header::redirect();
        }

        $oAffModel = new AffiliateModel;
        $oAff = $oAffModel->readProfile($iProfileId, DbTableName::AFFILIATE);

        // Birth date with the date format for the date picker
        $sBirthDate = (new CDateTime)->get($oAff->birthDate)->date('Y-m-d');

        $oForm = new \PFBC\Form('form_aff_edit_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_aff_edit_account', 'form_aff_edit_account'));
        $oForm->addElement(new \PFBC\Element\Token('edit_account'));

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            $oForm->addElement(
                new \PFBC\Element\HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get('affiliate', 'admin', 'browse') . '">' . t('Back to Browse Affiliates') . '</a></p>')
            );
        }

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h2 class="underline">' . t('Global Information:') . '</h2>'));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="cinnabar-red">' . t('All your information must be accurate and valid.') . '</p>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Your First Name:'), 'first_name', ['id' => 'name_first', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oAff->firstName, 'required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_first"></span>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Your Last Name:'), 'last_name', ['id' => 'name_last', 'onblur' => 'CValid(this.value,this.id)', 'value' => $oAff->lastName, 'required' => 1, 'validation' => new \PFBC\Validation\Name]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error name_last"></span>'));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Username:'), 'username', ['description' => t('For security reasons, you cannot change your username.'), 'disabled' => 'disabled', 'value' => $oAff->username]));

        $oForm->addElement(new \PFBC\Element\Email(t('Your Email:'), 'mail', ['description' => t('For security reasons and to avoid spam, you cannot change your email address.'), 'disabled' => 'disabled', 'value' => $oAff->email]));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error phone"></span>'));

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            // For security reasons, only admins can change profile gender
            $oForm->addElement(
                new \PFBC\Element\Radio(
                    t('Gender:'),
                    'sex',
                    [
                        GenderTypeUserCore::MALE => t('Man'),
                        GenderTypeUserCore::FEMALE => t('Woman')
                    ],
                    [
                        'value' => $oAff->sex,
                        'required' => 1
                    ]
                )
            );
        }

        if (self::isAdminLoggedAndUserIdExists($oHttpRequest)) {
            // For security reasons, only admins can change the date of birth
            $oForm->addElement(new \PFBC\Element\Date(t('Your Date of Birth:'), 'birth_date', ['id' => 'birth_date', 'onblur' => 'CValid(this.value, this.id)', 'value' => $sBirthDate, 'validation' => new \PFBC\Validation\BirthDate, 'required' => 1]));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<span class="input_error birth_date"></span>'));
        }

        // Generate dynamic fields
        $oFields = $oAffModel->getInfoFields($iProfileId, DbTableName::AFFILIATE_INFO);
        foreach ($oFields as $sColumn => $sValue) {
            $oForm = (new DynamicFieldCoreForm($oForm, $sColumn, $sValue))->generate();
        }

        $oForm->addElement(new \PFBC\Element\Button(t('Save'), 'submit', ['icon' => 'check']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
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

        return (new Session)->get('affiliate_id');
    }

    /**
     * Check if the admin is logged.
     *
     * @param HttpRequest $oHttpRequest
     *
     * @return bool
     */
    private static function isAdminLoggedAndUserIdExists(HttpRequest $oHttpRequest)
    {
        return AdminCore::auth() && !Affiliate::auth() &&
            $oHttpRequest->getExists('profile_id');
    }
}
