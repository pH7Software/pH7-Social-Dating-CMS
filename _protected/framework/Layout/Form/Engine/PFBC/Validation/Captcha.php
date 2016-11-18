<?php
/**
 * This code has been modified by made this code pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class Captcha extends \PFBC\Validation
{
    protected $message;
    protected $privateKey;

    public function __construct($privateKey, $message = '')
    {
        $this->privateKey = $privateKey;

        if (!empty($message))
            $this->message = t('The code of Captcha entered was incorrect. Please re-try.');
    }

    public function isValid($value)
    {
        require_once(__DIR__ . '/../Resources/recaptchalib.php');
        $resp = recaptcha_check_answer ($this->privateKey, $_SERVER['REMOTE_ADDR'], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

        return ($resp->is_valid) ? true : false;
    }
}
