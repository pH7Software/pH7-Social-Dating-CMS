<?php
/**
 * This code has been made by pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;

class BirthDate extends \PFBC\Validation
{
    protected $iMin, $iMax;

    public function __construct()
    {
        parent::__construct();

        $this->iMin = DbConfig::getSetting('minAgeRegistration');
        $this->iMax = DbConfig::getSetting('maxAgeRegistration');
        $this->message = t('You must be %0% to %1% years to register on the site.', $this->iMin, $this->iMax );
    }

    /**
     * @return boolean
     */
    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue)) {
            return true;
        }

        return $this->oValidate->birthDate($sValue, $this->iMin, $this->iMax);
   }
}
