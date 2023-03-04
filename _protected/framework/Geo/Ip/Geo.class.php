<?php
/**
 * @title            Ip localization Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2021, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Geo / Ip
 */

namespace PH7\Framework\Geo\Ip;

defined('PH7') or exit('Restricted access');

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use MaxMind\Db\Reader\InvalidDatabaseException;
use PH7\Framework\Ip\Ip;

class Geo
{
    const DATABASE_FILENAME = 'GeoLite2-City.mmdb';
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
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
     *
     * @return string|null Country Code.
     */
    public static function getCountryCode($sIpAddress = null)
    {
        try {
            $sCountryCode = static::get($sIpAddress)->country->isoCode;
        } catch (AddressNotFoundException | InvalidDatabaseException $oE) {
            $sCountryCode = null;
        }

        return $sCountryCode;
    }

    /**
     * Get the Zip Code (postal code).
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
     *
     * @return string|null Zip Code.
     */
    public static function getZipCode($sIpAddress = null)
    {
        try {
            $sZipCode = static::get($sIpAddress)->postal->code;
        } catch (AddressNotFoundException | InvalidDatabaseException $oE) {
            $sZipCode = null;
        }

        return $sZipCode;
    }

    /**
     * Get the country name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
     *
     * @return string|null Country Name. GeoLite2 and GeoIP2 names are UTF-8 encoded.
     */
    public static function getCountry($sIpAddress = null)
    {
        try {
            $sCountryName = static::get($sIpAddress)->country->name;
        } catch (AddressNotFoundException | InvalidDatabaseException $oE) {
            $sCountryName = null;
        }

        return $sCountryName;
    }

    /**
     * Get the city name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
     *
     * @return string|null City Name. GeoLite2 and GeoIP2 names are UTF-8 encoded.
     */
    public static function getCity($sIpAddress = null)
    {
        try {
            $sCity = static::get($sIpAddress)->city->name;
        } catch (AddressNotFoundException | InvalidDatabaseException $oE) {
            $sCity = null;
        }

        return $sCity;
    }

    /**
     * Get the state (region) name.
     *
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
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
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will use the current customer who visits the site.
     *
     * @return \GeoIp2\Model\City
     *
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    private static function get($sIpAddress = null)
    {
        // Set current IP address if none are given
        if (empty($sIpAddress)) {
            $sIpAddress = Ip::get();
        }

        // Set a valid IP if the (invalid) local IP one is given
        if ($sIpAddress === Ip::DEFAULT_IP) {
            $sIpAddress = self::DEFAULT_VALID_IP;
        }

        $oReader = new Reader(__DIR__ . PH7_DS . self::DATABASE_FILENAME);

        return @$oReader->city($sIpAddress);
    }

    /**
     * Block cloning.
     */
    private function __clone()
    {
    }
}
