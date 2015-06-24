<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Mvc\Model\DbConfig, PH7\Framework\Security\CSRF\Token as T;

class Token extends Hidden
{

    private $sName;

    public function __construct($sName)
    {
        if (!$this->_isEnabled())
            return; // If it's disabled, we stop the execution of the class

        $this->sName = $sName;
        parent::__construct('security_token', (new T)->generate($this->sName));
    }

    public function render()
    {
        if (!$this->_isEnabled())
            return; // If it's disabled, we stop the execution of the class

        $this->validation[] = new \PFBC\Validation\Token($this->sName);
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
