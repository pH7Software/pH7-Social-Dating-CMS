<?php
/**
 * Class made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Name extends Validation
{
    public function __construct()
    {
        parent::__construct();

        $this->message = t("%element% must be a valid name.");
    }

    public function isValid($sValue): bool
    {
        if ($this->isNotApplicable($sValue)) {
            return true;
        }

        return is_string($sValue) && $this->oValidate->name($sValue);
    }
}
