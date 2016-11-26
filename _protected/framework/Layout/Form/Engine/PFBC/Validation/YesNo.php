<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class YesNo extends \PFBC\Validation
{
    public function __construct() {
        $this->message = t('Error: You must accept our %element%.');
    }

    public function isValid($sValue) {
        return ($this->isNotApplicable($sValue) || $sValue == 1);
    }
}
