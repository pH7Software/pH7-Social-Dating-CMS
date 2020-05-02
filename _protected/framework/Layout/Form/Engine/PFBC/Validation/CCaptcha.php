<?php
/**
 * This code has been made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Spam\Captcha\Captcha;

class CCaptcha extends Validation
{
    /** @var bool */
    private $bIsCaseSensitive;

    public function __construct()
    {
        $this->bIsCaseSensitive = (bool)DbConfig::getSetting('captchaCaseSensitive');
        $this->message = t('The code of Captcha entered was incorrect. Please re-try.');
    }

    /**
     * @param string $sValue
     *
     * @return bool
     */
    public function isValid($sValue)
    {
        return (new Captcha)->check($sValue, $this->bIsCaseSensitive);
    }
}
