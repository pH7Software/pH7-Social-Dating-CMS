<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Timezone extends Select
{

    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = array();
        $aKeys = ['-12', '-11', '-10', '-9', '-8', '-7', '-6', '-5', '-4', '-3.5', '-3', '-2', '-1', '+0', '+1', '+2', '+3', '+3.5', '+4', '+4.5', '+5', '+5.5', '+6', '+7', '+8', '+8.75', '+9', '+9.5', '+10', '+10.5', '+11', '+11.5', '+12', '+12.75', '+13', '+14'];

        foreach($aKeys as $sValue)
            $aOptions[$sValue] = t($sValue);

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }

}
