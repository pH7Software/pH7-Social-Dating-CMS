<?php

/**
 * Created by Pierre-Henry Soria.
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;

class BirthDate extends Validation
{
    /** @var int */
    protected $iMin;

    /** @var int */
    protected $iMax;

    public function __construct()
    {
        parent::__construct();

        $this->iMin = DbConfig::getSetting('minAgeRegistration');
        $this->iMax = DbConfig::getSetting('maxAgeRegistration');
        $this->message = t('You must be from %0% to %1% years old to join the service.', $this->iMin, $this->iMax);
    }

    /**
     * @return bool
     */
    public function isValid($mDate)
    {
        if ($mDate === null || $mDate === '') {
            return true;
        }

        return is_string($mDate) && $this->oValidate->birthDate($mDate, $this->iMin, $this->iMax);
    }
}
