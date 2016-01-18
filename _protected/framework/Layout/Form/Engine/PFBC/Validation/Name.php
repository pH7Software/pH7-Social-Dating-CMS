<?php
/**
 * Class made by pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class Name extends \PFBC\Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t("Error: %element% must be a valid name. The name entered doesn't seem correct.");
    }

    public function isValid($sValue)
    {
        return ($this->isNotApplicable($sValue) || $this->oValidate->name($sValue));
    }
}
