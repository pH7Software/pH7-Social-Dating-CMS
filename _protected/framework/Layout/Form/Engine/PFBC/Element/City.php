<?php
/**
 * By @pH-7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class City extends Select
{

    const UTAH_CITIES = [
        '' => '--Select State--',
        ''
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        parent::__construct($sLabel, $sName, self::UTAH_CITIES, $aProperties);
    }
}
