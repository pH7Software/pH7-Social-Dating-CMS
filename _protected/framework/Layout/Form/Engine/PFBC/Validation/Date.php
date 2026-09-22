<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Date extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('%element% must contain a valid date.');
    }

    public function isValid($sValue)
    {
        // A field missing from the request arrives as null, which DateTime no longer accepts in PHP 9
        return $this->isNotApplicable($sValue) || $this->oValidate->date($sValue);
    }
}
