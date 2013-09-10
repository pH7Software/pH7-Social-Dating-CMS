<?php
/**
 * @title            Ip localization Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Geo / Ip
 * @version          1.0
 */

namespace PH7\Framework\Geo\Ip;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;

class Geo
{

    const GET_REGION = 'region';

    /**
     * Static Class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Get Country Code (e.g., en, it, es, ru, fr, ...).
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return string Country Code.
     */
    public static function getCountryCode($sIpAddress = null)
    {
        return @static::get($sIpAddress)->country_code;
    }

    /**
     * Get Zip Code (postal code).
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return integer Zip Code.
     */
    public static function getZipCode($sIpAddress = null)
    {
        return @static::get($sIpAddress)->postal_code;
    }

    /**
     * Get Latitude.
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return float Latitude.
     */
    public static function getLatitude($sIpAddress = null)
    {
        return @static::get($sIpAddress)->latitude;
    }

    /**
     * Get Longitude.
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return float Longitude.
     */
    public static function getLongitude($sIpAddress = null)
    {
        return @static::get($sIpAddress)->longitude;
    }

    /**
     * Get Country Name.
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return string Country Name.
     */
    public static function getCountry($sIpAddress = null)
    {
        // Encode to UTF8 for Latin and other characters of the GeoIP database are displayed correctly.
        return utf8_encode(@static::get($sIpAddress)->country_name);
    }

    /**
     * Get City Name.
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return string City Name.
     */
    public static function getCity($sIpAddress = null)
    {
        // Encode to UTF8 for Latin and other characters of the GeoIP database are displayed correctly.
        return utf8_encode(@static::get($sIpAddress)->city);
    }

    /**
     * Get State (region) Name.
     *
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @return string State Name.
     */
    public static function getState($sIpAddress = null)
    {
        // Encode to UTF8 for Latin and other characters of the GeoIP database are displayed correctly.
        return utf8_encode(@static::get($sIpAddress, self::GET_REGION));
    }

    /**
     * Get Geo Ip Data Information.
     *
     * @access protected
     * @param string $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site. Default is NULL
     * @param string $sOption Default NULL
     * @return object
     */
    protected static function get($sIpAddress = null, $sOption = null)
    {
        require_once 'geoip.inc.php';
        require_once 'geoipcity.inc.php';
        require_once 'geoipregionvars.php';
        $oGeoIp = geoip_open(__DIR__ . '/GeoLiteCity.dat', GEOIP_STANDARD);

        $oRecord = geoip_record_by_addr($oGeoIp, (!empty($sIpAddress) ? $sIpAddress : Ip::get()));

        if ($sOption == static::GET_REGION)
            return $GEOIP_REGION_NAME[$oRecord->country_code][$oRecord->region];
        else
            return $oRecord;
    }

    /**
     * Block cloning.
     *
     * @access private
     */
    private function __clone() {}

}
