<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class Url extends \PFBC\Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: %element% must contain a URL (e.g., <a href="http://meetlovelypeople.com">http://www.meetlovelypeople.com</a>).');
    }

    public function isValid($sValue)
    {
        return ($this->isNotApplicable($sValue) || $this->oValidate->url($sValue));
    }
}
