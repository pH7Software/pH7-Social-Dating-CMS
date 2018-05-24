<?php

/**
 * @title            Birthday Cron Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>, Polyna-Maude R.-Summerside <polynamaude@gmail.com>
 * @copyright        (c) 2013-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 24H
 * @version          1.0
 */
namespace PH7;

defined ( 'PH7' ) or exit ( 'Restricted access' );

use Exception;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Error\Logger;

class BannedCoreCron extends Cron {
	/**
	 * Web client used to fetch IPs
	 *
	 * @var \GuzzleHttp\Client
	 */
	private $webClient;

	/**
	 * Banned IP filename
	 *
	 * @var string
	 */
	private $bannedFileName;
	/**
	 * Contain new blocked IP just fetched
	 *
	 * @var array
	 */
	private $ipNew;

	/**
	 * Contain existing blocked IP
	 *
	 * @var array
	 */
	private $ipOld;

	/**
	 * IP extracting regulat expression
	 *
	 * @var string
	 */
	private $ipRegExp;

	/**
	 * Contain the URL of the service we call to get banned IP
	 * Currently filled at instantiation staticly, will use config file later
	 *
	 * @var array
	 */
	private $svcUrl;
	public function __construct() {
		parent::__construct ();

		/**
		 * Set valid IP regular expression using lazy mode (false)
		 *
		 * @var \PH7\BannedCoreCron $ipRegExp
		 */
		$this->ipRegExp = self::regexpIP ();

		/**
		 * Set the banned IP file name to the same file as used by PH7\Framework\Security\Ban\Ban
		 *
		 * @var \PH7\BannedCoreCron $bannedFileName
		 */
		$this->bannedFileName = PH7_PATH_APP_CONFIG . Ban::DIR . Ban::IP_FILE;
		/**
		 *
		 * @var \PH7\BannedCoreCron $svcUrl
		 */
		$this->svcUrl = array (
				'https://www.blocklist.de/downloads/export-ips_all.txt',
				'http://www.badips.com/get/list/ssh/2',
		);
		$this->doProcess();
	}

	/**
	 * Get the job done !
	 */
	protected function doProcess() {
		/**
		 * Process each web url we have in the $svcUrl array
		 */
		foreach ( $this->svcUrl as $url ) {
			/**
			 * Each url we have for Web Service
			 *
			 * @var string $url
			 */
			try {
				/**
				 * If we don't get true then we have an error
				 */
				if (! $this->callWebService ( $url )) {
					(new Logger ())->msg ( 'Error calling web service for banned IP url name :' . $url );
				}
				/**
				 * We catch exception so we can continue if one service fail
				 */
			} catch ( Exception $e ) {
				(new Logger ())->msg ( 'Error calling web service for banned IP url name :' . $url );
			}
		}

		/**
		 * Process the currently banned IP
		 */
		$this->processExistingIP ();
		/**
		 * Merge both IPs and filter out doubles
		 */
		$this->processIP ();

		/**
		 * Wrie the new banned IP file
		 */
		if (! $this->writeIP ()) {
			(new Logger ())->msg ( 'Error writting new banned IP file' );
		}
	}

	/**
	 * Call the web service with the given url and add received IP into $ipNew
	 *
	 * @param string $url
	 */
	private function callWebService($url) {
		if (is_null ( $this->webClient )) {
			$this->webClient = new \GuzzleHttp\Client ();
		}

		/**
		 * If we don't have a valid array to put address into, we create it.
		 */
		if (! is_array ( $this->ipNew )) {
			$this->ipNew = array ();
		}
		/**
		 * Call the webClient with the url
		 */
		$inbound = $this->webClient->get ( $url );

		/**
		 * Check we get a valid response
		 */
		if ($inbound->getStatusCode () !== '200') {
			return false;
		}

		/**
		 * Get the body and detach into a stream
		 */
		$bannedIPs = $inbound->getBody ()->detach ();

		/**
		 * Process the received IP
		 */
		while ( $bannedIP = fgets ( $bannedIPs ) ) {
			/**
			 * Trim the ip from return carriage and new line then add to the current array
			 */
			$this->ipNew [] = rtrim ( $bannedIP, "\n\r" );
		}
		return true;
	}

	/**
	 * Process existing banned IP file and only keep valide IP adress
	 */
	private function processExistingIP() {
		/**
		 * We fill a temporary array with current adress
		 */
		$aBans = file ( $this->bannedFileName );
		$this->ipOld = array ();
		foreach ( $aBans as $ban ) {
			/**
			 * Array containing return IP adress
			 *
			 * @var array $ips
			 */
			$ips = preg_grep ( $this->ipRegExp, $ban );
			/**
			 * check if $ip empty in case we processed a text line
			 */
			if (! empty ( $ips )) {
				/**
				 * Use a foreach loop in case we have more than one IP per line
				 */
				foreach ( $ips as $ip ) {
					$this->ipOld [] = $ip;
				}
			}
		}
	}

	/**
	 * Read both IPs array, merge and extract only unique one
	 */
	private function processIP() {
		$newIP = array ();
		$newIP = array_unique ( array_merge ( $this->ipNew, $this->ipOld ), SORT_STRING );
		$this->ipNew = $newIP;
	}

	/**
	 * Return a valid IPv4 regular expression
	 * Using strict reject octal form (leading zero)
	 *
	 * @param bool $strict
	 * @return string
	 */
	static public function regexpIP($strict = false) {
		if (strict) {
			/**
			 * Regular Expression representing a valid IPv4 class address
			 * Rejecting octal form (leading zero)
			 *
			 * @var string $ipRegExp
			 */
			$ipRegExp = '/(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
			$ipRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
			$ipRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.';
			$ipRegExp .= '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])/';
		} else {
			/**
			 *
			 * Regular Expression representing a valid IPv4 class address
			 * We accept leading 0 but they normally imply octal so we shouldn't !!!
			 *
			 * @var string $ipRegExp
			 */
			$ipRegExp = '/(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
			$ipRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
			$ipRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)\.';
			$ipRegExp .= '(25[0-5]|2[0-9][0-9]|[01]?[0-9][0-9]?)/';
		}
		return $ipRegExp;
	}

	/**
	 * Write IPs to banned ip file
	 *
	 * @return boolean
	 */
	private function writeIP() {
		$outfile = fopen ( $this->bannedFileName, 'w+' );
		if (empty ( $this->ipNew ) || ! is_array ( $this->ipNew )) {
			return false;
		}
		foreach ( $this->ipNew as $ip ) {
			fwrite ( $outfile, $ip . "\n" );
		}
		fclose ( $outfile );
		return true;
	}
}