<?php
/**
 * @title            BannedIP Cron Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>, Polyna-Maude R.-Summerside <polynamaude@gmail.com>
 * @copyright        (c) 2013-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use Exception;
use GuzzleHttp\Client;
use PH7\Framework\Error\Logger;
use PH7\Framework\File\Permission\Chmod;
use PH7\Framework\Security\Ban\Ban;
use PH7\JustHttp\StatusCode;

/** Reset time limit and increase memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class BannedIpCoreCron extends Cron
{
    /**
     * Contain the URL of the remote services we call to get the list of accurate banned IPs.
     * Currently filled at instantiation statically, will use config file later
     *
     * @var array
     */
    private const SVC_URLS = [
        'https://www.blocklist.de/downloads/export-ips_all.txt',
        'https://www.badips.com/get/list/ssh/2?age=30d',
        'https://www.rjmblocklist.com/free/badips.txt'
    ];

    private const BANNED_IP_FILE_PATH = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;

    private const ERROR_CALLING_WEB_SERVICE_MESSAGE = '%s: Error while calling: %s';
    private const ERROR_ADD_BANNED_IP_MESSAGE = '%s: Error while writing new banned IP addresses.';

    private const NEW_LINE = "\r\n";

    /**
     * Web client used to fetch IPs.
     */
    private Client $oWebClient;

    /**
     * Contain new blocked IP just fetched.
     */
    private array $aNewIps = [];

    /**
     * Contain existing blocked IPs.
     */
    private array $aOldIps = [];

    /**
     * IP extracting regular expression.
     */
    private string $sIpRegExp;

    public function __construct()
    {
        parent::__construct();

        $this->oWebClient = new Client();

        /**
         * Set valid IP regular expression using lazy mode ($bStrict = false)
         */
        $this->sIpRegExp = self::regexpIps();

        $this->doProcess();

        echo t('The banned IP list has been updated!');
    }

    protected function doProcess(): void
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
                    $this->logErrorMessage(
                        sprintf(self::ERROR_CALLING_WEB_SERVICE_MESSAGE, static::class, $sUrl)
                    );
                }

                /**
                 * Catch exception, so we can continue if one service fails
                 */
            } catch (Exception $oExcept) {
                $this->logErrorMessage(
                    sprintf(self::ERROR_CALLING_WEB_SERVICE_MESSAGE, static::class, $sUrl)
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
         * Update the banned IP file
         */
        if (!$this->writeIps()) {
            $this->logErrorMessage(
                sprintf(self::ERROR_ADD_BANNED_IP_MESSAGE, static::class)
            );
        }
    }

    /**
     * Call the web service with the given url and add received IP into $aNewIps
     */
    private function callWebService(string $sUrl): bool
    {
        $oRemoteResource = $this->oWebClient->get($sUrl);

        /**
         * Check we get a valid response
         */
        if ($oRemoteResource->getStatusCode() !== StatusCode::OK) {
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
            $this->aNewIps[] = rtrim($sBannedIp, self::NEW_LINE);
        }

        return true;
    }

    /**
     * Process existing banned IP file and only keep validating IP addresses.
     */
    private function processExistingIps(): bool
    {
        /**
         * We fill a temporary array with current IP addresses
         */
        if (!$aBannedIps = file(self::BANNED_IP_FILE_PATH)) {
            return false;
        }

        $aIps = preg_grep($this->sIpRegExp, $aBannedIps);

        if (is_array($aIps)) {
            /**
             * Use a foreach loop in case we have more than one IP per line
             */
            foreach ($aIps as $sIp) {
                $this->aOldIps[] = $sIp;
            }
            return true;
        }

        return false;
    }

    /**
     * Read both IPs array, merge and extract only the unique ones.
     */
    private function processIps(): void
    {
        $aNewIps = array_unique(array_merge($this->aNewIps, $this->aOldIps), SORT_STRING);
        $this->aNewIps = $aNewIps;
    }

    /**
     * Return a valid IPv4 regular expression
     * Using strict reject octal form (leading zero)
     */
    public static function regexpIps(bool $bStrict = false): string
    {
        if ($bStrict) {
            /**
             * Regular Expression representing a valid IPv4 class address
             * Rejecting octal form (leading zero)
             */
            $sIpRegExp = '/(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
            $sIpRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])/';
        } else {
            /**
             * Regular Expression representing a valid IPv4 class address
             * We accept leading 0, but they normally imply octal so we shouldn't!
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
     */
    private function writeIps(): bool
    {
        if ($this->invalidNewIps()) {
            return false;
        }

        $this->file->chmod(self::BANNED_IP_FILE_PATH, Chmod::MODE_ALL_EXEC);

        foreach ($this->aNewIps as $sIp) {
            $this->addIp($sIp);
        }

        return $this->removeDuplicatedEntries();
    }

    /**
     * Add single address IP into the banned IPs list.
     */
    private function addIp(string $sIpAddress): void
    {
        $this->file->putFile(self::BANNED_IP_FILE_PATH, $sIpAddress . self::NEW_LINE, FILE_APPEND);
    }

    private function invalidNewIps(): bool
    {
        return empty($this->aNewIps) || !is_array($this->aNewIps);
    }

    private function removeDuplicatedEntries(): bool
    {
        if (!$aBannedIps = file(self::BANNED_IP_FILE_PATH)) {
            return false;
        }

        // Remove duplicated rows
        $aBannedIps = array_unique($aBannedIps);

        $sBannedIps = implode(self::NEW_LINE, $aBannedIps);

        return (bool)$this->file->save(self::BANNED_IP_FILE_PATH, $sBannedIps);
    }

    private function logErrorMessage(string $sMessage): void
    {
        (new Logger())->msg($sMessage);
    }
}

// Get the job done!
new BannedIpCoreCron;
