<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Security\CSRF\Token as T;

class Token extends Hidden
{

    private $sName;

    public function __construct($sName)
    {
        $this->sName = $sName;
        parent::__construct('security_token', (new T)->generate($this->sName));
    }

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Token($this->sName);
        parent::render();
    }

}
