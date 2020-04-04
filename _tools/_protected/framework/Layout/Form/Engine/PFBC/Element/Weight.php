<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

use PH7\Framework\Math\Measure\Weight as W;

class Weight extends Select
{
    const MIN_WEIGHT = 30;
    const MAX_WEIGHT = 150;

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = [];

        for ($iWeight = self::MIN_WEIGHT; $iWeight <= self::MAX_WEIGHT; $iWeight += 2) {
            $aOptions[$iWeight] = (new W($iWeight))->display();
        }

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }
}
