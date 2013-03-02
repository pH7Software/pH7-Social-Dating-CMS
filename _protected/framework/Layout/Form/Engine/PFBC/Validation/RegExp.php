<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

class RegExp extends \PFBC\Validation {
    protected $pattern;

    public function __construct($pattern, $message = '') {
        $this->pattern = $pattern;
        $this->message = t('Error: %element% contains invalid characters. Here is the rule to be observed: "%0%"', $this->pattern);
        parent::__construct($message);
    }

    public function isValid($value) {
        if($this->isNotApplicable($value) || preg_match('#^'.$this->pattern.'$#', $value))
            return true;
        return false;
    }
}
