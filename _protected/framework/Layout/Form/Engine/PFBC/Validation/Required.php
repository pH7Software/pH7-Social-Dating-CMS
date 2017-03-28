<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

class Required extends \PFBC\Validation
{
    public function __construct()
    {
        $this->message = t('Error: %element% is a required field.');
    }

    /**
     * @param mixed (array or string) $mValue
     * @return boolean
     */
    public function isValid($mValue)
    {
        $bValid = false; // Default value

        if (!is_null($mValue)) {
            if (is_array($mValue)) {
                foreach ($mValue as $sItem) {
                    if (!$bValid = $this->isValid($sItem)) {
                        return false;
                      }
                }
            } else {
                $bValid = ($mValue !== '');
            }
        }

        return $bValid;
    }
}
