<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Phone extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: Your phone number is incorrect. Please enter full phone number with area code (e.g., +44768374890).');
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->phone($sValue);
    }
}
