<?php
/**
 * This file has been made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Layout\Form\Message;
use PH7\Framework\Security\CSRF\Token as SecurityToken;

class Token extends Validation
{
    // Import `Message` trait
    use Message;

    private string $sName;

    public function __construct(string $sName)
    {
        $this->message = self::errorTokenMsg();
        $this->sName = $sName;
    }

    /**
     * @param string $sValue
     *
     * @return bool Returns TRUE if the token is validated, FALSE otherwise.
     */
    public function isValid($sValue): bool
    {
        return (new SecurityToken)->check($this->sName, $sValue);
    }
}
