<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class RegExp extends \PFBC\Validation
{
    protected $pattern;

    public function __construct($sPattern, $sMsg = '')
    {
        $this->pattern = $sPattern;
        $this->message = t('Error: %element% contains invalid characters. Here is the rule to be followed: "%0%"', $this->pattern);
        parent::__construct($sMsg);
    }

    public function isValid($sValue)
    {
        if($this->isNotApplicable($sValue) || preg_match('#^'.$this->pattern.'$#', $sValue))
            return true;
        return false;
    }
}
