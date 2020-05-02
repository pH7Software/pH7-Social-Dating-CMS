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
        $this->message = t('Error: %element% must contain a valid date.');
    }

    public function isValid($sValue)
    {
        return $this->oValidate->date($sValue);
    }
}
