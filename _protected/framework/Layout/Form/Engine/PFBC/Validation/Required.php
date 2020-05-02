<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;

class Required extends Validation
{
    public function __construct()
    {
        $this->message = t('Error: %element% is a required field.');
    }

    /**
     * @param array|string $mValue
     *
     * @return boolean
     */
    public function isValid($mValue)
    {
        $bValid = false; // Default value

        if ($mValue !== null) {
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
