<?php
/**
 * @title            Validate Ajax Class
 * @desc             Checks the data entered by a form via Ajax and indicates if there are errors (Asynchronous data).
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Ajax
 * @version          1.2
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Str\Str;

class ValidateCoreAjax
{
    private
    $_oStr, // String Object
    $_oValidate,   // Validate object
    $_oExistsModel, // Object ot the ExistsModel Class
    $_sMsg = ' ', // Default value message
    $_iStatus = 0; // By default the status is in failure (0 = fail, 1 = OK)

    public function __construct()
    {
        $this->_oStr = new Str;
        $this->_oValidate = new Validate;
        $this->_oExistsModel = new ExistsCoreModel;
    }

    /**
     * Support for AJAX validation, checking the values.
     *
     * @param string $sInputVal
     * @param string $sFieldId
     * @param string $sParam1
     * @param string $sParam2
     * @return void
     */
    public function form($sInputVal, $sFieldId, $sParam1, $sParam2)
    {
        $sInputVal = $this->_oStr->escape($sInputVal, true);

        // Determine the field to validate and perform validation.
        if (strstr($sFieldId, 'str_'))
        {   // Check Text
            $this->txt($sInputVal, $sParam1, $sParam2);
        }
        elseif (strstr($sFieldId, 'name_'))
        {
            $this->name($sInputVal);
        }
        elseif (strstr($sFieldId, 'email'))
        {   // Check email address.
             $this->email($sInputVal, $sParam1, $sParam2);
        }
        elseif (strstr($sFieldId, 'url'))
        {  // Check the url address.
            $this->url($sInputVal);
        }
        elseif (strstr($sFieldId, 'phone'))
        {
            // Check the phone number
            $this->phone($sInputVal);
        }
        else
        {
            switch ($sFieldId)
            {
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
                    Framework\Http\Http::setHeadersByCode(400);
                    exit('Bad Request Error!');
            }
        }

        echo json_encode(array('status'=>$this->_iStatus,'msg'=>$this->_sMsg,'fieldId'=>$sFieldId));
    }


    /**
     * Validate the username (must not be empty or already known).
     *
     * @access protected
     * @param string $sValue
     * @param string $sTable
     * @return void
     */
    protected function username($sValue, $sTable)
    {
        // Checks and corrects the table if it is incorrect.
        if ($sTable !== 'Members' && $sTable !== 'Affiliates' && $sTable !== 'Admins') $sTable = 'Members';

        $this->_iStatus = ($this->_oValidate->username($sValue, DbConfig::getSetting('minUsernameLength'), DbConfig::getSetting('maxUsernameLength'), $sTable)) ? 1 : 0;
        $this->_sMsg = ($this->_iStatus) ? t('This Username is available!') : t('Sorry, but this Username is not available.');
    }

    /**
     * Validate the email address.
     *
     * @access protected
     * @param string $sValue
     * @param string $sParam
     * @param string $sTable
     * @return void
     */
    protected function email($sValue, $sParam, $sTable)
    {
        // Checks and corrects the table if it is incorrect.
        if ($sTable !== 'Members' && $sTable !== 'Affiliates' && $sTable !== 'Admins') $sTable = 'Members';

        if (!$this->_oValidate->email($sValue))
        {
            $this->_sMsg = t('Invalid Email Address!');
        }
        elseif ($sParam == 'guest' && $this->_oExistsModel->email($sValue, $sTable))
        {
            $this->_sMsg = t('This email already used by another member.');
        }
        elseif ($sParam == 'user' && !$this->_oExistsModel->email($sValue, $sTable))
        {
            $this->_sMsg = sprintf(t('Oops! "%s" is not associated with any %site_name% account.'), substr($sValue,0,50));
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('Valid Email!');
        }
    }

    /**
     * Validation of the password.
     *
     * @access protected
     * @param string $sValue
     * @return void
     */
    protected function password($sValue)
    {
        $iMin =  DbConfig::getSetting('minPasswordLength');
        $iMax = DbConfig::getSetting('maxPasswordLength');

        if (!$this->_oValidate->password($sValue, $iMin, $iMax))
        {
            $this->_sMsg = sprintf(t('Your Password has to contain from %d to %d characters.'), $iMin, $iMax);
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('Correct Password!');
        }
    }

    /**
     * Validation of the date and birthday.
     *
     * @access protected
     * @param string $sValue
     * @return void
     */
    protected function birthDate($sValue)
    {
        $iMin = DbConfig::getSetting('minAgeRegistration');
        $iMax = DbConfig::getSetting('maxAgeRegistration');

        if (!$this->_oValidate->date($sValue))
        {
            $this->_sMsg = t('Your must enter a date valid (Month/Day/Year).');
        }
        elseif (!$this->_oValidate->birthDate($sValue, $iMin, $iMax))
        {
            $this->_sMsg = sprintf(t('You must be %d to %d years to register on the site.'), $iMin, $iMax);
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('OK!');
        }
    }

    /**
     * Check whether the type of a variable is string.
     *
     * @access protected
     * @param string $sValue
     * @param integer $iMin Default NULL
     * @param integer $iMax Default NULL
     * @return void
     */
    protected function txt($sValue, $iMin = null, $iMax = null)
    {
        $sValue = trim($sValue);
        if (!empty($sValue))
        {
            if (!empty($iMin) && $this->_oStr->length($sValue) < $iMin)
            {
                $this->_sMsg = sprintf(t('Please, enter %d character(s) or more.'), $iMin);
            }
            elseif (!empty($iMax) && $this->_oStr->length($sValue) > $iMax)
            {
                $this->_sMsg = sprintf(t('Please, enter %d character(s) or less.'), $iMax);
            }
            elseif (!is_string($sValue))
            {
                $this->_sMsg = t('Please enter a string.');
            }
            else
            {
                $this->_iStatus = 1;
                $this->_sMsg = t('OK!');
            }
        }
        else
        {
          $this->_sMsg = t('This field is required!');
        }
    }

    /**
     * Validation of names.
     *
     * @access protected
     * @param string $sValue
     * @return void
     */
    protected function name($sValue)
    {
        if (!$this->_oValidate->name($sValue))
        {
            $this->_sMsg = t("Your name doesn't seem to be correct.");
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('OK!');
        }
    }

    /**
     * Validation of the url.
     *
     * @access protected
     * @param string $sValue
     * @return void
     */
    protected function url($sValue)
    {
        if (!$this->_oValidate->url($sValue))
        {
            $this->_sMsg = t('Your must enter a valid url (e.g. http://www.coolonweb.com).');
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('OK!');
        }
    }

    /**
     * Validate Captcha.
     *
     * @access protected
     * @return string $sValue
     * @return void
     */
    protected function captcha($sValue)
    {
        if ((new Framework\Security\Spam\Captcha\Captcha)->check($sValue))
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('OK!');
        }
        else
        {
            $this->_sMsg = t('Captcha check failed!');
        }
    }

    /**
     * Validate international phone numbers in EPP format.
     *
     * @access protected
     * @return string $sValue
     * @return void
     */
    protected function phone($sValue)
    {
        if (!$this->_oValidate->phone($sValue))
        {
            $this->_sMsg = t('Please enter a correct phone number with area code!');
        }
        else
        {
            $this->_iStatus = 1;
            $this->_sMsg = t('OK!');
        }
    }

    /**
     * Validation of the acceptance of the terms of use.
     *
     * @access protected
     * @return string $sValue
     * @return void
     */
    protected function terms($sValue)
    {
        if ($sValue != 'true')
            $this->_sMsg = t('You must read and approve the terms of use!');
        else
            $this->_iStatus = 1;
    }
}


$oHttpRequest = new Http;

if ($oHttpRequest->postExists('fieldId')) {
    (new ValidateCoreAjax)->form(
        $oHttpRequest->post('inputVal'),
        $oHttpRequest->post('fieldId'),
        $oHttpRequest->post('param1'),
        $oHttpRequest->post('param2')
    );
}

unset($oHttpRequest);
