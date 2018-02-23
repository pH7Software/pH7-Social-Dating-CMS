<?php
/**
 * This file has been made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PFBC\Validation\Token as ValidationToken;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\CSRF\Token as SecurityToken;

class Token extends Hidden
{
    /** @var string */
    private $sName;

    /**
     * @param string $sName
     */
    public function __construct($sName)
    {
        if (!$this->isEnabled()) {
            return; // If it's disabled, we stop the execution of the class
        }

        $this->sName = $sName;
        parent::__construct('security_token', (new SecurityToken)->generate($this->sName));
    }

    public function render()
    {
        if (!$this->isEnabled()) {
            return; // If it's disabled, we stop the execution of the class
        }

        $this->validation[] = new ValidationToken($this->sName);
        parent::render();
    }

    /**
     * Check if the CSRF security token for forms is enabled.
     *
     * @return bool Returns TRUE if the security token is enabled, FALSE otherwise.
     */
    private function isEnabled()
    {
        // Check if the CSRF security token for forms is enabled
        return DbConfig::getSetting('securityToken');
    }
}
