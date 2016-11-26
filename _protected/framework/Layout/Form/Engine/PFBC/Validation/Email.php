<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class Email extends \PFBC\Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: %element% must contain an email address.');
    }

    public function isValid($sValue)
    {
        return ($this->isNotApplicable($sValue) || $this->oValidate->email($sValue));
    }
}
