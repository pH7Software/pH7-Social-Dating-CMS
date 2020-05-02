<?php
/**
 * Created by Pierre-Henry Soria
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Mvc\Model\DbConfig;

class BirthDate extends Validation
{
    const DATE_PATTERN = 'm/d/Y';

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
     * @param string $sDate
     *
     * @return bool
     */
    public function isValid($sDate)
    {
        if ($this->isNotApplicable($sDate)) {
            return true;
        }

        $sDate = (new CDateTime)->get($sDate)->date(self::DATE_PATTERN);

        return $this->oValidate->birthDate($sDate, $this->iMin, $this->iMax);
    }
}
