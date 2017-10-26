<?php
/**
 * Many changes have been made in this file.
 * By Pierre-Henry SORIA.
 */

namespace PFBC;

use PH7\Framework\Security\Validate\Validate;

abstract class Validation extends Base
{
    /** @var Validate */
    protected $oValidate;

    /** @var string */
    protected $message;

    public function __construct($message = '')
    {
        $this->oValidate = new Validate;

        if (!empty($message)) {
            $this->message = t('%element% is invalid.');
        }
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isNotApplicable($value)
    {
        return ($value === null || is_array($value) || $value === '');
    }

    abstract public function isValid($value);
}
