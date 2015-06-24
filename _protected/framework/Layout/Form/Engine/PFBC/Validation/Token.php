<?php
/**
 * This code has been made by pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;

use PH7\Framework\Layout\Form\Form as F, PH7\Framework\Security\CSRF\Token as T;

class Token extends \PFBC\Validation
{

    private $sName;

    public function __construct($sName)
    {
        $this->message = F::errorTokenMsg();
        $this->sName = $sName;
    }

    /**
     * @return boolean Returns TRUE if the token is validated, FALSE otherwise.
     */
    public function isValid($sValue)
    {
        return (new T)->check($this->sName, $sValue);
    }
}
