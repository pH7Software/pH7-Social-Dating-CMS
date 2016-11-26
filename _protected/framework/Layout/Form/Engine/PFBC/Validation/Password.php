<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;

class Password extends \PFBC\Validation
{
    protected $iMin, $iMax;

    public function __construct()
    {
        parent::__construct();

        $this->iMin = DbConfig::getSetting('minPasswordLength');
        $this->iMax = DbConfig::getSetting('maxPasswordLength');
        $this->message = t('Error: Your password has to contain from %0% to %1% characters.', $this->iMin, $this->iMax);
    }

    public function isValid($sValue)
    {
        if($this->isNotApplicable($sValue)) return true;
        return $this->oValidate->password($sValue, $this->iMin, $this->iMax);
    }
}
