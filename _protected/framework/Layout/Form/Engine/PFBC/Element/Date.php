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

        $iCurrentYear = (int)date('Y');
        $iOldestAllowedBirthYear = $iCurrentYear - (int)DbConfig::getSetting('maxAgeRegistration');
        $iYoungestAllowedBirthYear = $iCurrentYear - (int)DbConfig::getSetting('minAgeRegistration');

        $this->attributes['min'] = $this->formatAsHtml5DateString($iOldestAllowedBirthYear, 1, 1);
        $this->attributes['max'] = $this->formatAsHtml5DateString($iYoungestAllowedBirthYear, 12, 31);

        parent::render();
    }

    private function formatAsHtml5DateString(int $iYear, int $iMonth, int $iDay): string
    {
        return sprintf('%d-%02d-%02d', $iYear, $iMonth, $iDay);
    }
}
