<?php
/**
 * @title            Ip localization Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Geo / Ip
 * @version          1.1
 */

namespace PH7\Framework\Geo\Ip;

defined('PH7') or exit('Restricted access');

use GeoIp2\Database\Reader;
use PH7\Framework\Ip\Ip;

class Geo
{
    const DEFAULT_VALID_IP = '128.101.101.101';

    /**
     * Static Class.
     */
    private function __construct()
    {
    }

    /**
     * Get the country ISO Code (e.g., GB, IT, ES, RU, FR, ...).
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return string Country Code.
     */
    public static function getCountryCode($sIpAddress = null)
    {
        return static::get($sIpAddress)->country->isoCode;
    }

    /**
     * Get the Zip Code (postal code).
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return integer Zip Code.
     */
    public static function getZipCode($sIpAddress = null)
    {
        return static::get($sIpAddress)->postal->code;
    }

    /**
     * Get the latitude.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return float Latitude.
     */
    public static function getLatitude($sIpAddress = null)
    {
        return static::get($sIpAddress)->location->latitude;
    }

    /**
     * Get the longitude.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return float Longitude.
     */
    public static function getLongitude($sIpAddress = null)
    {
        return static::get($sIpAddress)->location->longitude;
    }

    /**
     * Get the country name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return string Country Name.
     */
    public static function getCountry($sIpAddress = null)
    {
        // Encode to UTF8 for Latin and other characters of the GeoIP database are displayed correctly.
        return utf8_encode(static::get($sIpAddress)->country->name);
    }

    /**
     * Get the city name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return string City Name.
     */
    public static function getCity($sIpAddress = null)
    {
        // Encode to UTF8 for Latin and other characters of the GeoIP database are displayed correctly.
        return utf8_encode(static::get($sIpAddress)->city->name);
    }

    /**
     * Get the state (region) name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return string State Name.
     */
    public static function getState($sIpAddress = null)
    {
        return ''; // Currently, with GeoIp2 under the free version, it is impossible to get the region names.
    }

    /**
     * Get Geo Ip Data Information.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site
     *
     * @return string|\GeoIp2\Model\City
     */
    protected static function get($sIpAddress = null)
    {
        $sIpAddr = ($sIpAddress !== null ? $sIpAddress : Ip::get());

        if ($sIpAddr === Ip::DEFAULT_IP) {
            // Set a valid IP address, if it's the invalid local one
            $sIpAddr = self::DEFAULT_VALID_IP;
        }

        $oReader = new Reader(__DIR__ . '/GeoLite2-City.mmdb');

        return @$oReader->city($sIpAddr);
    }

    /**
     * Block cloning.
     */
    private function __clone()
    {
    }
}
