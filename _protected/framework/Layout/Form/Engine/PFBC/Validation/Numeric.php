<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Numeric extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: %element% must be numeric.');
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->numeric($sValue);
    }
}
