<?php
/**
 * Many changes have been made in this file.
 * By Pierre-Henry Soria <https://ph7.me>
 */

namespace PFBC;

use PH7\Framework\Security\Validate\Validate;

abstract class Validation extends Base
{
    /** @var Validate */
    protected $oValidate;

    /** @var string */
    protected $message;

    /**
     * @param string $message
     */
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

    /**
     * @param array|string|bool|null $value
     *
     * @return bool
     */
    public function isNotApplicable($value)
    {
        return ($value === null || is_array($value) || $value === '');
    }

    abstract public function isValid($value);
}
