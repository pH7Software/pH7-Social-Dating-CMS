<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Security\Ban\Ban;

class Email extends \PFBC\Validation
{

    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: %element% must contain an email address.');
    }

    public function isValid($sValue)
    {
        $sEmailHost = strrchr($sValue, '@');
        return ($this->isNotApplicable($sValue) || (!Ban::isEmail($sValue) && !Ban::isEmail($sEmailHost) && $this->oValidate->email($sValue)));
    }
}
