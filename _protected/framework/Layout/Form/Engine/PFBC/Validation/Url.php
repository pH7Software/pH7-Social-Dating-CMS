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
        $this->message = t('Error: %element% must contain a URL (e.g., <a href="http://ph7cms.com">http://ph7cms.com</a>).');
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->url($sValue);
    }
}
