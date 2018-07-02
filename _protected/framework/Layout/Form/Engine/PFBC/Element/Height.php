<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

use PH7\Framework\Math\Measure\Height as H;

class Height extends Select
{
    const MIN_HEIGHT = 120;
    const MAX_HEIGHT = 220;

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = [];

        for ($iHeight = self::MIN_HEIGHT; $iHeight <= self::MAX_HEIGHT; $iHeight += 2) {
            $aOptions[$iHeight] = (new H($iHeight))->display();
        }

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }
}
