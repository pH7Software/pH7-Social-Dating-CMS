<?php

/**
 * This file has been made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PFBC\Validation\Token as ValidationToken;
use PH7\Framework\Security\CSRF\Token as SecurityToken;

class Token extends Hidden
{
    private string $sName;

    public function __construct(string $sName)
    {
        $this->sName = $sName;
        parent::__construct('security_token', (new SecurityToken())->generate($this->sName));
    }

    public function render()
    {
        $this->validation[] = new ValidationToken($this->sName);
        parent::render();
    }
}
