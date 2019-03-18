<?php
/**
 * By @pH-7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class City extends Select
{
    const UTAH_CITIES = [
        '' => '--Select City--',
        'Alpine',
        'American Fork',
        'Bountiful',
        'Brigham City',
        'Canyon Rim',
        'Cedar City',
        'Centerville',
        'Clearfield',
        'Clinton',
        'Cottonwood Heights',
        'Cottonwood West',
        'Draper',
        'East Millcreek',
        'Farmington',
        'Grantsville',
        'Heber',
        'Highland',
        'Holladay',
        'Hurricane',
        'Hyrum',
        'Kaysville',
        'Kearns',
        'Layton',
        'Lehi',
        'Lindon',
        'Little Cottonwood Creek Valley',
        'Logan',
        'Magna',
        'Midvale',
        'Millcreek',
        'Mount Olympus',
        'Murray',
        'North Logan',
        'North Ogden',
        'North Salt Lake',
        'Ogden',
        'Oquirrh',
        'Orem',
        'Park City',
        'Payson',
        'Pleasant Grove',
        'Price',
        'Provo',
        'Richfield',
        'Riverdale',
        'Riverton',
        'Roy',
        'Salt Lake City',
        'Sandy',
        'Smithfield',
        'South Jordan',
        'South Ogden',
        'South Salt Lake',
        'Spanish Fork',
        'Springville',
        'St. George',
        'Summit Park',
        'Syracuse',
        'Taylorsville',
        'Tooele',
        'Vernal',
        'Washington',
        'Washington Terrace',
        'West Jordan',
        'West Point',
        'West Valley City',
        'Woods Cross'
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
