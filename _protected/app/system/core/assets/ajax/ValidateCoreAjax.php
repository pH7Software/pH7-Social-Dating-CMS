<?php
/**
 * @title            Validate Ajax Class
 * @desc             Checks the data entered by a form via Ajax and indicates if there are errors (Asynchronous data).
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Ajax
 * @version          1.2
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\Spam\Captcha\Captcha;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Str\Str;
use Teapot\StatusCode;

class ValidateCoreAjax
{
    /** @var Str */
    private $oStr;

    /** @var Validate */
    private $oValidate;

    /** @var ExistsCoreModel */
    private $oExistsModel;

    /** @var string Default message value */
    private $sMsg = ' ';

    /** @var int By default the status is in "failure" (0 = fail, 1 = OK) */
    private $iStatus = 0; //

    public function __construct()
    {
        $this->oStr = new Str;
        $this->oValidate = new Validate;
        $this->oExistsModel = new ExistsCoreModel;
    }

    /**
     * Support for AJAX validation, checking the values.
     *
     * @param string $sInputVal
     * @param string $sFieldId
     * @param string $sParam1
     * @param string $sParam2
     *
     * @return void
     */
    public function form($sInputVal, $sFieldId, $sParam1, $sParam2)
    {
        $sInputVal = $this->oStr->escape($sInputVal, true);

        // Determine the field to validate and perform validation.
        if (strstr($sFieldId, 'str_')) {   // Check Text
            $this->txt($sInputVal, $sParam1, $sParam2);
        } elseif (strstr($sFieldId, 'name_')) {
            $this->name($sInputVal);
        } elseif (strstr($sFieldId, 'email')) {   // Check email address.
            $this->email($sInputVal, $sParam1, $sParam2);
        } elseif (strstr($sFieldId, 'url')) {  // Check the url address.
            $this->url($sInputVal);
        } elseif (strstr($sFieldId, 'phone')) {
            // Check the phone number
            $this->phone($sInputVal);
        } else {
            switch ($sFieldId) {
                // Check that the username is valid.
                case 'username':
                    $this->username($sInputVal, $sParam1);
                    break;

                // Check Password.
                case 'password':
                    $this->password($sInputVal);
                    break;

                // Check of the date of birth
                case 'birth_date':
                    $this->birthDate($sInputVal);
                    break;

                // Check the captcha.
                case 'ccaptcha':
                    $this->captcha($sInputVal);
                    break;

                // Check acceptance of the terms of use.
                case 'terms-0':
                    $this->terms($sInputVal);
                    break;

                // If we receive another invalid value, we display a message with a HTTP header.
                default:
                    Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                    exit('Bad Request Error!');
            }
        }

        echo json_encode(['status' => $this->iStatus, 'msg' => $this->sMsg, 'fieldId' => $sFieldId]);
    }


    /**
     * Validate the username (must not be empty or already known).
     *
     * @param string $sValue
     * @param string $sTable
     *
     * @return void
     */
    protected function username($sValue, $sTable)
    {
        if (!$this->isDbTableValid($sTable)) {
            $sTable = DbTableName::MEMBER;
        }

        $iMin = DbConfig::getSetting('minUsernameLength');
        $iMax = DbConfig::getSetting('maxUsernameLength');

        $this->iStatus = $this->oValidate->username($sValue, $iMin, $iMax, $sTable) ? 1 : 0;
        $this->sMsg = $this->iStatus ? t('This Username is available!') : t('Sorry, but this Username is not available.');
    }

    /**
     * Validate the email address.
     *
     * @param string $sValue
     * @param string $sParam
     * @param string $sTable
     *
     * @return void
     */
    protected function email($sValue, $sParam, $sTable)
    {
        if (!$this->isDbTableValid($sTable)) {
            $sTable = DbTableName::MEMBER;
        }

        if (!$this->oValidate->email($sValue)) {
            $this->sMsg = t('Invalid Email Address!');
        } elseif ($sParam === 'guest' && $this->oExistsModel->email($sValue, $sTable)) {
            $this->sMsg = t('This email already used by another member.');
        } elseif ($sParam === 'user' && !$this->oExistsModel->email($sValue, $sTable)) {
            $this->sMsg = t('Oops! "%0%" is not associated with any %site_name% account.', substr($sValue, 0, 50));
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('Valid Email!');
        }
    }

    /**
     * Validation of the password.
     *
     * @param string $sValue
     *
     * @return void
     */
    protected function password($sValue)
    {
        $iMin = DbConfig::getSetting('minPasswordLength');
        $iMax = DbConfig::getSetting('maxPasswordLength');

        if (!$this->oValidate->password($sValue, $iMin, $iMax)) {
            $this->sMsg = t('Your Password has to contain from %0% to %1% characters.', $iMin, $iMax);
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('Correct Password!');
        }
    }

    /**
     * Validation of the date and birthday.
     *
     * @param string $sValue
     *
     * @return void
     */
    protected function birthDate($sValue)
    {
        $iMin = DbConfig::getSetting('minAgeRegistration');
        $iMax = DbConfig::getSetting('maxAgeRegistration');

        // Format the date to the needed format
        $sValue = (new CDateTime)->get($sValue)->date('m/d/Y');

        if (!$this->oValidate->date($sValue)) {
            $this->sMsg = t('Your must enter a valid date (Month-Day-Year).');
        } elseif (!$this->oValidate->birthDate($sValue, $iMin, $iMax)) {
            $this->sMsg = t('You must be %0% to %1% years to register on the site.', $iMin, $iMax);
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('OK!');
        }
    }

    /**
     * Check whether the type of a variable is string.
     *
     * @param string $sValue
     * @param int $iMin
     * @param int $iMax
     *
     * @return void
     */
    protected function txt($sValue, $iMin = null, $iMax = null)
    {
        $sValue = trim($sValue);
        if (!empty($sValue)) {
            if (!empty($iMin) && $this->oStr->length($sValue) < $iMin) {
                $this->sMsg = t('Please, enter %0% character(s) or more.', $iMin);
            } elseif (!empty($iMax) && $this->oStr->length($sValue) > $iMax) {
                $this->sMsg = t('Please, enter %0% character(s) or less.', $iMax);
            } elseif (!is_string($sValue)) {
                $this->sMsg = t('Please enter a string.');
            } else {
                $this->iStatus = 1;
                $this->sMsg = t('OK!');
            }
        } else {
            $this->sMsg = t('This field is required!');
        }
    }

    /**
     * Validation of names.
     *
     * @param string $sValue
     *
     * @return void
     */
    protected function name($sValue)
    {
        if (!$this->oValidate->name($sValue)) {
            $this->sMsg = t("Your name doesn't seem to be correct.");
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('OK!');
        }
    }

    /**
     * Validation of the url.
     *
     * @param string $sValue
     *
     * @return void
     */
    protected function url($sValue)
    {
        if (!$this->oValidate->url($sValue)) {
            $this->sMsg = t('Your must enter a valid url (e.g., http://ph7cms.com).');
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('OK!');
        }
    }

    /**
     * Validate Captcha.
     *
     * @return string $sValue
     *
     * @return void
     */
    protected function captcha($sValue)
    {
        $bIsCaseSensitive = (bool)DbConfig::getSetting('captchaCaseSensitive');

        if ((new Captcha)->check($sValue, $bIsCaseSensitive)) {
            $this->iStatus = 1;
            $this->sMsg = t('OK!');
        } else {
            $this->sMsg = t('Captcha check failed!');
        }
    }

    /**
     * Validate international phone numbers in EPP format.
     *
     * @return string $sValue
     *
     * @return void
     */
    protected function phone($sValue)
    {
        if (!$this->oValidate->phone($sValue)) {
            $this->sMsg = t('Please enter a correct phone number with area code!');
        } else {
            $this->iStatus = 1;
            $this->sMsg = t('OK!');
        }
    }

    /**
     * Validation of the acceptance of the terms of use.
     *
     * @param string $sValue
     *
     * @return void
     */
    protected function terms($sValue)
    {
        if ($sValue !== 'true') {
            $this->sMsg = t('You need to read and accept the terms of use.');
        } else {
            $this->iStatus = 1;
        }
    }

    /**
     * @param string $sTable The table name.
     *
     * @return bool
     */
    private function isDbTableValid($sTable)
    {
        return in_array($sTable, DbTableName::USER_TABLES, true);
    }
}

$oHttpRequest = new HttpRequest;
if ($oHttpRequest->postExists('fieldId')) {
    (new ValidateCoreAjax)->form(
        $oHttpRequest->post('inputVal'),
        $oHttpRequest->post('fieldId'),
        $oHttpRequest->post('param1'),
        $oHttpRequest->post('param2')
    );
}
unset($oHttpRequest);
