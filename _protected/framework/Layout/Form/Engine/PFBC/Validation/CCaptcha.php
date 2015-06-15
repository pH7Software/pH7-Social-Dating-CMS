<?php
/**
 * This code has been made by pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Security\Spam\Captcha\Captcha;

class CCaptcha extends \PFBC\Validation
{
    public function __construct()
    {
        $this->message = t('The code of Captcha entered was incorrect. Please re-try.');
    }

    /**
     * @return boolean
     */
    public function isValid($sValue)
    {
        return (new Captcha)->check($sValue);
    }
}
