<?php
/**
 * We made many changes in this code.
 * By pH7.
 */
namespace PFBC;
use PH7\Framework\Security\Validate\Validate;

abstract class Validation extends Base
{

    protected $oValidate, $message;

    public function __construct($message = '')
    {
        $this->oValidate = new Validate;

        if(!empty($message))
            $this->message = t('%element% is invalid.');
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isNotApplicable($value)
    {
        return (is_null($value) || is_array($value) || $value === '');
    }

    public abstract function isValid($value);

    public function __destruct()
    {
        unset($this->oValidate, $this->message);
    }

}
