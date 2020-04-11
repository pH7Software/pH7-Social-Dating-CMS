<?php
/**
 * @title            Ip localization Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
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
            // TODO: When support PHP 7.1, specify multiple exceptions using "|" pipe
        } catch (AddressNotFoundException $oE) {
            $sCountryCode = '';
        } catch (InvalidDatabaseException $oE) {
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
            // TODO: When support PHP 7.1, specify multiple exceptions using "|" pipe
        } catch (AddressNotFoundException $oE) {
            $sZipCode = '';
        } catch (InvalidDatabaseException $oE) {
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

            // TODO: When support PHP 7.1, specify multiple exceptions using "|" pipe
        } catch (AddressNotFoundException $oE) {
            $sCountryName = '';
        } catch (InvalidDatabaseException $oE) {
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

            // TODO: When support PHP 7.1, specify multiple exceptions using "|" pipe
        } catch (AddressNotFoundException $oE) {
            $sCity = '';
        } catch (InvalidDatabaseException $oE) {
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
     * @param string|null $sIpAddress Specify an IP address. If NULL, it will address the current customer who visits the site.
     *
     * @return \GeoIp2\Model\City
     *
     * @throws AddressNotFoundException
     * @throws InvalidDatabaseException
     */
    private static function get($sIpAddress = null)
    {
        $sIpAddr = ($sIpAddress !== null ? $sIpAddress : Ip::get());

        if ($sIpAddr === Ip::DEFAULT_IP) {
            // Set a valid IP address, if it's the invalid local one
            $sIpAddr = self::DEFAULT_VALID_IP;
        }

        $oReader = new Reader(__DIR__ . PH7_DS . self::DATABASE_FILENAME);

        return @$oReader->city($sIpAddr);
    }

    /**
     * Block cloning.
     */
    private function __clone()
    {
    }
}
