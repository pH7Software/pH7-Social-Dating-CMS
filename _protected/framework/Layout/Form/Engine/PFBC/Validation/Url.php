<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Url extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('%element% must contain a URL (e.g. <a href="http://ph7builder.com">http://ph7builder.com</a>).');
    }

    public function isValid($sValue): bool
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->url($sValue);
    }
}
