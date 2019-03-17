<?php
/**
 * By @pH-7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class City extends Select
{

    const UTAH_CITIES = [
        '' => '--Select State--',
        'Salt Lake',
        'West Valley',
        'Provo',
        'West Jordan',
        'Orem',
        'Sandy',
        'Ogden',
        'St. George',
        'Layton',
        'Taylorsville',
        'South Jordan',
        'Lehi',
        'Logan',
        'Murray',
        'Draper',
        'Bountiful',
        'Riverton',
        'Roy'
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aCities = array_combine(self::UTAH_CITIES, self::UTAH_CITIES);
        parent::__construct($sLabel, $sName, $aCities, $aProperties);
    }
}
