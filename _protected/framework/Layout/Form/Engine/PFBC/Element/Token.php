<?php
/**
 * This file has been made by pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Security\CSRF\Token as SecurityToken,
PFBC\Validation\Token as ValidationToken;

class Token extends Hidden
{

    private $sName;

    public function __construct($sName)
    {
        if (!$this->_isEnabled())
            return; // If it's disabled, we stop the execution of the class

        $this->sName = $sName;
        parent::__construct('security_token', (new SecurityToken)->generate($this->sName));
    }

    public function render()
    {
        if (!$this->_isEnabled())
            return; // If it's disabled, we stop the execution of the class

        $this->validation[] = new ValidationToken($this->sName);
        parent::render();
    }

    /**
     * Check if the CSRF security token for forms is enabled.
     *
     * @return boolean Returns TRUE if the security token is enabled, FALSE otherwise.
     */
    private function _isEnabled()
    {
        // Check if the CSRF security token for forms is enabled
        return DbConfig::getSetting('securityToken');
    }

}
