<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Model
 */

namespace PH7;

use PH7\Framework\Mvc\Model\Engine\Model;

class GeoIpCoreModel extends Model
{
    /*
     *  In development. Feel free to help me! <https://github.com/pH7Software/pH7-Social-Dating-CMS>
     */

    public function getCountry($sWhere)
    {
        return $this->orm->select('GeoCountry', 'countryId, countryTitle')->find('countryId', $sWhere)->orderBy('countryTitle')->execute();
    }

    public function getState($sWhere)
    {
        return $this->orm->select('GeoState', 'stateId, stateTitle')->find('stateId', $sWhere)->orderBy('stateTitle')->execute();
    }

    public function getCity($sWhere)
    {
        return $this->orm->select('GeoCity', 'cityId, cityTitle')->find('cityId', $sWhere)->orderBy('cityTitle')->execute();
    }
}
