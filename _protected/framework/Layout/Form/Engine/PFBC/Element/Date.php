<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Mvc\Model\DbConfig;

class Date extends Textbox
{

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        $iCurrentYear = date('Y');
        $iMin = $iCurrentYear - DbConfig::getSetting('maxAgeRegistration');
        $iMax = $iCurrentYear - DbConfig::getSetting('minAgeRegistration');

        echo 'jQuery("#', $this->attributes['id'], '").datepicker({dateFormat:\'mm/dd/yy\',defaultDate:-9862,changeMonth:true,changeYear:true,yearRange:\''.$iMin.':'.$iMax.'\'});';
    }

    public function render()
    {
        $this->validation[] = new \PFBC\Validation\Date;
        parent::render();
    }

}
