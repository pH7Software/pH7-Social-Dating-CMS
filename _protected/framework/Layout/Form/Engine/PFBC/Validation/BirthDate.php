<?php
/**
 * This code has been made by pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\DbConfig;

class BirthDate extends \PFBC\Validation
{
    protected $iMin, $iMax;

    public function __construct()
    {
        parent::__construct();

        $this->iMin = DbConfig::getSetting('minAgeRegistration');
        $this->iMax = DbConfig::getSetting('maxAgeRegistration');
        $this->message = t('You must be from %0% to %1% years old to join the service.', $this->iMin, $this->iMax);
    }

    /**
     * @return boolean
     */
    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue)) {
            return true;
        }

        $sValue = (new CDateTime)->get($sValue)->date('m/d/Y');

        return $this->oValidate->birthDate($sValue, $this->iMin, $this->iMax);
    }
}
