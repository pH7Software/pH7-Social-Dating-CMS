<?php
/**
 * I made changes in this file (by Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PH7\Framework\Mvc\Model\DbConfig;

class Date extends Textbox
{
    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Date;
        $this->attributes['type'] = 'date';

        $iCurrentYear = date('Y');
        $iMin = $iCurrentYear - DbConfig::getSetting('maxAgeRegistration');
        $iMax = $iCurrentYear - DbConfig::getSetting('minAgeRegistration');
        $this->attributes['min'] = $iMin;
        $this->attributes['max'] = $iMax;

        parent::render();
    }
}
