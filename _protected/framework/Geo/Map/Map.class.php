<?php
/**
 * @title            Google Map Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Geo / Map
 */

namespace PH7\Framework\Geo\Map;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class Map extends Api
{
    public function __construct()
    {
        parent::__construct();

        $this->initializeGoogleMaps();
    }

    private function initializeGoogleMaps()
    {
        $this->setEnableWindowZoom(true);
        $this->setMapType(DbConfig::getSetting('mapType'));
        $this->setLang(PH7_LANG_CODE);
    }
}
