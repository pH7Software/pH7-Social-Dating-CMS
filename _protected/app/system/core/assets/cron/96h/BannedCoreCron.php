<?php
/**
 * @title            BannedIP Cron Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>, Polyna-Maude R.-Summerside <polynamaude@gmail.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use Exception;
use GuzzleHttp\Client;
use PH7\Framework\Error\Logger;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\Security\Ban\Ban;

/** Reset time limit and increase memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class BannedCoreCron extends Cron
{
    /**
     * Contain the URL of the remote services we call to get the list of accurate banned IPs.
     * Currently filled at instantiation statically, will use config file later
     *
     * @var array
     */
    const SVC_URLS = [
        'https://www.blocklist.de/downloads/export-ips_all.txt',
        'https://www.badips.com/get/list/ssh/2?age=30d',
        'https://www.rjmblocklist.com/free/badips.txt'
    ];

    const BANNED_IP_FILE_PATH = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;

    const ERROR_CALLING_WEB_SERVICE_MESSAGE = 'Error calling web service for banned IP URL name: %s';
    const ERROR_ADD_BANNED_IP_MESSAGE = 'Error writing new banned IP addresses.';

    /**
     * Web client used to fetch IPs
     *
     * @var Client
     */
    private $oWebClient;

    /**
     * Contain new blocked IP just fetched
     *
     * @var array
     */
    private $aNewIps;

    /**
     * Contain existing blocked IP
     *
     * @var array
     */
    private $aOldIps;

    /**
     * IP extracting regular expression.
     *
     * @var string
     */
    private $sIpRegExp;

    public function __construct()
    {
        parent::__construct();

        /**
         * Set valid IP regular expression using lazy mode (false)
         */
        $this->sIpRegExp = self::regexpIps();

        $this->doProcess();

        echo t('Banned IPs list updated!');
    }

    protected function doProcess()
    {
        /**
         * Process each web url we have in the $svcUrl array
         */
        foreach (self::SVC_URLS as $sUrl) {
            try {
                /**
                 * If we don't get TRUE, then, we have an error...
                 */
                if (!$this->callWebService($sUrl)) {
                    (new Logger())->msg(
                        sprintf(self::ERROR_CALLING_WEB_SERVICE_MESSAGE, $sUrl)
                    );
                }

                /**
                 * Catch exception, so we can continue if one service fails
                 */
            } catch (Exception $oExcept) {
                (new Logger())->msg(
                    sprintf(self::ERROR_CALLING_WEB_SERVICE_MESSAGE, $sUrl)
                );
            }
        }

        /**
         * Process the currently banned IPs
         */
        $this->processExistingIps();

        /**
         * Merge both IPs and filter out doubles
         */
        $this->processIps();

        /**
         * Write the new banned IP file
         */
        if (!$this->writeIps()) {
            (new Logger())->msg(self::ERROR_ADD_BANNED_IP_MESSAGE);
        }
    }

    /**
     * Call the web service with the given url and add received IP into $aNewIps
     *
     * @param string $sUrl
     *
     * @return bool
     */
    private function callWebService($sUrl)
    {
        if (is_null($this->oWebClient)) {
            $this->oWebClient = new Client();
        }

        if ($this->invalidNewIp()) {
            $this->aNewIps = [];
        }

        $oRemoteResource = $this->oWebClient->get($sUrl);

        /**
         * Check we get a valid response
         */
        if ($oRemoteResource->getStatusCode() !== 200) {
            return false;
        }

        /**
         * Get the body and detach into a stream
         */
        $rBannedIps = $oRemoteResource->getBody()->detach();

        /**
         * Process the received IP
         */
        while ($sBannedIp = fgets($rBannedIps)) {
            /**
             * Trim the IP from return carriage and new line, then add to the current array
             */
            $this->aNewIps[] = rtrim($sBannedIp, "\r\n");
        }

        return true;
    }

    /**
     * Process existing banned IP file and only keep validating IP addresses.
     *
     * @return void
     */
    private function processExistingIps()
    {
        /**
         * We fill a temporary array with current IP addresses
         */
        $aBannedIps = file(self::BANNED_IP_FILE_PATH);
        $this->aOldIps = [];
        $aIps = preg_grep($this->sIpRegExp, $aBannedIps);

        if (!empty($aIps)) {
            /**
             * Use a foreach loop in case we have more than one IP per line
             */
            foreach ($aIps as $sIp) {
                $this->aOldIps[] = $sIp;
            }
        }
    }

    /**
     * Read both IPs array, merge and extract only unique one.
     *
     * @return void
     */
    private function processIps()
    {
        $aNewIps = array_unique(array_merge($this->aNewIps, $this->aOldIps), SORT_STRING);
        $this->aNewIps = $aNewIps;
    }

    /**
     * Return a valid IPv4 regular expression
     * Using strict reject octal form (leading zero)
     *
     * @param bool $bStrict
     *
     * @return string
     */
    public static function regexpIps($bStrict = false)
    {
        if ($bStrict) {
            /**
             * Regular Expression representing a valid IPv4 class address
             * Rejecting octal form (leading zero)
             *
             * @var string $sIpRegExp
             */
            $sIpRegExp = '/(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])/';
        } else {
            /**
             * Regular Expression representing a valid IPv4 class address
             * We accept leading 0 but they normally imply octal so we shouldn't !!!
             *
             * @var string $sIpRegExp
             */
            $sIpRegExp = '/(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
            $sIpRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
            $sIpRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
            $sIpRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)/';
        }

        return $sIpRegExp;
    }

    /**
     * Write IPs to the configs/banned/ip.txt file.
     *
     * @return bool
     */
    private function writeIps()
    {
        if ($this->invalidNewIp()) {
            return false;
        }

        $this->file->chmod(self::BANNED_IP_FILE_PATH, Chmod::MODE_ALL_EXEC);

        foreach ($this->aNewIps as $sIp) {
            $this->addIp($sIp);
        }

        return true;
    }

    /**
     * Add single address IP into the banned IPs list.
     *
     * @param string $sIpAddress
     *
     * @return void
     */
    private function addIp($sIpAddress)
    {
        file_put_contents(self::BANNED_IP_FILE_PATH, $sIpAddress . "\n", FILE_APPEND);
    }

    /**
     * @return bool
     */
    private function invalidNewIp()
    {
        return empty($this->aNewIps) || !is_array($this->aNewIps);
    }
}

// Get the job done!
new BannedCoreCron;
