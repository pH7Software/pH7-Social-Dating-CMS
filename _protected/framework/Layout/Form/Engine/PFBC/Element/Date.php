<?php
/**
 * I made changes in this code (by Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PH7\Framework\Config\Config;
use PH7\Framework\Mvc\Model\DbConfig;

class Date extends Textbox
{
    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        $iCurrentYear = date('Y');
        $iMin = $iCurrentYear - DbConfig::getSetting('maxAgeRegistration');
        $iMax = $iCurrentYear - DbConfig::getSetting('minAgeRegistration');
        $sDateFormat = Config::getInstance()->values['language.application']['date_format'];

        echo 'jQuery("#', $this->attributes['id'], '").datepicker({dateFormat:\''. $sDateFormat . '\',defaultDate:-9862,changeMonth:true,changeYear:true,yearRange:\''.$iMin.':'.$iMax.'\'});';
    }

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Date;
        parent::render();
    }
}
