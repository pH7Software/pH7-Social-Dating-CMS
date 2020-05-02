<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\Framework\Str\Str as FwkStr;

class Str extends Validation
{
    /** @var FwkStr */
    protected $oStr;

    /** @var int|null */
    protected $iMin;

    /** @var int|null */
    protected $iMax;

    /**
     * @param int|null $iMin
     * @param int|null $iMax
     */
    public function __construct($iMin = null, $iMax = null)
    {
        $this->oStr = new FwkStr;
        $this->iMin = $iMin;
        $this->iMax = $iMax;
    }

    /**
     * @param string $sValue Check if the variable type is a valid string.
     *
     * @return bool
     */
    public function isValid($sValue)
    {
        $sValue = trim($sValue);

        if ($this->isNotApplicable($sValue)) {
            return true; // If the field not required
        }

        if (!empty($this->iMin) && $this->oStr->length($sValue) < $this->iMin) {
            $this->message = t('Error: %element% must be at least %0% character(s) long.', $this->iMin);
            return false;
        }

        if (!empty($this->iMax) && $this->oStr->length($sValue) > $this->iMax) {
            $this->message = t('Error: %element% cannot exceed %0% character(s).', $this->iMax);
            return false;
        }

        if (!is_string($sValue)) {
            $this->message = t('Please enter a string.');
            return false;
        }

        return true;
    }
}
