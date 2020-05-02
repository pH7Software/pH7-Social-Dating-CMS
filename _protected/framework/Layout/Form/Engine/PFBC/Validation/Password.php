<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;

class Password extends Validation
{
    /** @var int */
    protected $iMin;

    /** @var int */
    protected $iMax;

    public function __construct()
    {
        parent::__construct();

        $this->iMin = DbConfig::getSetting('minPasswordLength');
        $this->iMax = DbConfig::getSetting('maxPasswordLength');
        $this->message = t('Error: Your password has to be from %0% to %1% characters long.', $this->iMin, $this->iMax);
    }

    /**
     * @param string $sValue
     *
     * @return bool
     */
    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue)) {
            return true;
        }

        return $this->oValidate->password($sValue, $this->iMin, $this->iMax);
    }
}
