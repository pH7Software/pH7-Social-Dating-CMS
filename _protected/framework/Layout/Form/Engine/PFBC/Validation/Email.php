<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Email extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('%element% must be a valid email address.');
    }

    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue)) {
            return true;
        }

        return is_string($sValue) && $this->oValidate->email($sValue);
    }
}
