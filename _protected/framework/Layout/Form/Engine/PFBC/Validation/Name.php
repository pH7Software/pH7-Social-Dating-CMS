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

        $this->message = t("Error: %element% must be a valid name. The name doesn't seem to be correct.");
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->name($sValue);
    }
}
