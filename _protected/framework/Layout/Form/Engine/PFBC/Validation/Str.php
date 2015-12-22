<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Str\Str as FwkStr;

class Str extends \PFBC\Validation
{

    protected $oStr, $iMin, $iMax;

    /**
     * @param integer $iMin Default NULL
     * @param integer $iMax Default NULL
     */
    public function __construct($iMin = null, $iMax = null)
    {
        $this->oStr = new FwkStr;
        $this->iMin = $iMin;
        $this->iMax = $iMax;
    }

    /**
     * @param string $sValue Check if the variable type is a valid string.
     * @return boolean
     */
    public function isValid($sValue)
    {
        $sValue = trim($sValue);

        if ($this->isNotApplicable($sValue)) return true; // Field not required

        if (!empty($this->iMin) && $this->oStr->length($sValue) < $this->iMin)
        {
            $this->message = t('Error: this %element% must contain %0% character(s) or more.', $this->iMin);
            return false;
        }
        elseif (!empty($this->iMax) && $this->oStr->length($sValue) > $this->iMax)
        {
            $this->message = t('Error: this %element% must contain %0% character(s) or less.', $this->iMax);
            return false;
        }
        elseif (!is_string($sValue))
        {
            $this->message = t('Please enter a string.');
            return false;
        }
        return true;
    }

}
