<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Math\Measure\Height as H;

class Height extends Select
{

    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = array();

        for($iHeight = 120; $iHeight <= 220; $iHeight+=2)
            $aOptions[$iHeight] = (new H($iHeight))->display();

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }

}
