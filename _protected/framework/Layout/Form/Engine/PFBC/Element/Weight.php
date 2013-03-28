<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Math\Measure\Weight as W;

class Weight extends Select
{

    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = array();

        for($iWeight = 30; $iWeight <= 150; $iWeight+=2)
            $aOptions[$iWeight] = (new W($iWeight))->display();

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }

}
