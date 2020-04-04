<?php
/**
 * @title            Analytics Class
 * @desc             Compute Stats of Site Referers.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Analytics
 * @version          0.6
 */

namespace PH7\Framework\Analytics;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\IOException;
use PH7\Framework\Navigation\Browser;

class Analytics extends StoreStats
{
    /**
     * OS list.
     *
     * @staticvar array $aOs
     */
    private static $aOs = [
        'windows nt 6.2' => 'Windows 8',
        'windows nt 6.1' => 'Windows Seven',
        'windows nt 6.0' => 'Windows Longhorn',
        'windows nt 5.2' => 'Windows 2003',
        'windows nt 5.0' => 'Windows 2000',
        'windows nt 5.1' => 'Windows XP',
        'winnt4.0|winnt 4.0|windows nt 4.0' => 'Windows NT 4.0',
        'winnt' => 'Windows NT',
        'win98|windows 98' => 'Windows 98',
        'win95|win32|windows 95' => 'Windows 95',
        'windows' => 'Unknown Windows OS',
        'os x' => 'Mac OS X',
        'ppc|macintosh' => 'Macintosh',
        'linux' => 'Linux',
        'debian' => 'Debian',
        'freebsd' => 'FreeBSD',
        'openbsd' => 'OpenBSD',
        'netbsd' => 'NetBSD',
        'ppc mac' => 'Power PC Mac',
        'sunos' => 'Sun Solaris',
        'beos' => 'BeOS',
        'apachebench' => 'ApacheBench',
        'aix' => 'AIX',
        'irix' => 'Irix',
        'bsdi' => 'BSDi',
        'osf' => 'DEC OSF',
        'hp-ux' => 'HP-UX',
        'gnu' => 'GNU/Linux',
        'unix' => 'Unknown Unix OS',

        /** Mobile **/
        'windows ce' => 'Windows CE', // Pocket PC
        'mobileexplorer' => 'Mobile Explorer',
        'openwave' => 'Open Wave',
        'opera mini|operamini' => 'Opera Mini',
        'sendo' => 'Sendo',
        'motorola' => 'Motorola',
        'benq' => 'BenQ',
        'lg' => 'LG',
        'blazer' => 'Treo',
        'nokia' => 'Nokia',
        'blackberry' => 'BlackBerry',
        'android' => 'Android',
        'iphone' => 'iPhone',
        'ipad' => 'iPad',
        'sony|ericsson' => 'Sony Ericsson',
        'vario' => 'Vario',
        'htc' => 'HTC',
        'samsung' => 'Samsung',
        'sharp' => 'Sharp',
        'amoi' => 'Amoi',
        'palm|palmsource|elaine' => 'Palm',
        'palmscape' => 'Palmscape',
        'symbian' => 'Symbian',
        'symbianos' => 'Symbian OS',
        'cocoon' => 'O2 Cocoon',
        'playstation portable' => 'PlayStation Portable (PSP)',
        'hiptop' => 'Danger Hiptop',
        'nec-' => 'NEC',
        'panasonic' => 'Panasonic',
        'philips' => 'Philips',
        'sagem' => 'Sagem',
        'sanyo' => 'Sanyo',
        'spv' => 'SPV',
        'zte' => 'ZTE',
        'xda' => 'XDA',
        'mda' => 'MDA',
        'digital paths' => 'Digital Paths',
        'avantgo' => 'AvantGo',
        'xiino' => 'Xiino',
        'novarra' => 'Novarra Transcoder',
        'vodafone' => 'Vodafone',
        'docomo' => 'NTT DoCoMo',
        'o2' => 'O2',
        'mobile' => 'Generic Mobile'
    ];

    /**
     * Web browser list.
     *
     * @staticvar array $aWebBrowsers
     */
    private static $aWebBrowsers = [
        'msie|internet explorer' => 'Internet Explorer',
        'firefox' => 'Firefox',
        'safari' => 'Safari',
        'chrome' => 'Google Chrome',
        'opera' => 'Opera',
        'konqueror' => 'Konqueror',
        'netscape' => 'Netscape',
        'seamonkey' => 'SeaMonkey',
        'epiphany' => 'Epiphany',
        'galeon' => 'Galeon',
        'mozilla' => 'Mozilla',
        'mozilla firebird' => 'Mozilla Firebird',
        'aol' => 'AOL',
        'lynx' => 'Lynx',
        'flock' => 'Flock',
        'omniWeb' => 'OmniWeb',
        'camino' => 'Camino',
        'shiira' => 'Shiira',
        'links' => 'Links',
        'hotjava' => 'HotJava',
        'amaya' => 'Amaya',
        'phoenix' => 'Phoenix',
        'firebird' => 'Firebird',
        'obigo' => 'Obigo',
        'netfront' => 'Netfront Browser',
        'mobilexplorer' => 'Mobile Explorer',
        'icab' => 'iCab',
        'ibrowse' => 'IBrowse'
    ];

    /**
     * Search engine bots list.
     *
     * @staticvar array $aRobots
     */
    private static $aRobots = [
        'googlebot' => 'Google',
        'bingbot|msnbot' => 'Bing',
        'slurp' => 'Inktomi Slurp',
        'yahoo' => 'Yahoo',
        'askjeeves' => 'AskJeeves',
        'fastcrawler' => 'FastCrawler',
        'lycos' => 'Lycos',
        'facebookexternalhit' => 'Facebook',
        'ph7hizupcrawler' => 'pH7Zup Crawler',
        'infoseek' => 'InfoSeek Robot 1.0',
        'duckduckbot' => 'DuckDuckGo',
        'qwantify' => 'Qwant'
    ];

    /**
     * Search engine IP bots list.
     *
     * @staticvar array $aIpRobots
     */
    private static $aIpRobots = [
        '74.125.130.105' => 'Google',
        '131.253.13.32' => 'Bing'
    ];

    /**
     * Keywords.
     *
     * @staticvar array $aKeywords
     */
    private static $aKeywords = [
        'google',
        'bing',
        'ask',
        'yahoo',
        'lycos',
        'aol',
        'duckduck',
        'qwant'
    ];

    /** @var null|string */
    private $sUserAgent;
    /** @var null|string */
    private $sReferer;
    /** @var string */
    private $sUserLang;

    /**
     * @throws IOException
     */
    public function __construct()
    {
        $oBrowser = new Browser;
        $this->sUserAgent = $oBrowser->getUserAgent();
        $this->sReferer = $oBrowser->getHttpReferer();
        $this->sUserLang = $oBrowser->getLanguage();
        unset($oBrowser);

        $this->init();
    }

    /**
     * Check OS
     *
     * @return string OS name.
     */
    public function checkOs()
    {
        $sOs = t('Unknown OS');

        foreach (self::$aOs as $sRegex => $sName) {
            if ($this->find($sRegex, $this->sUserAgent)) {
                $sOs = $sName;
                break;
            }
        }

        return $sOs;
    }

    /**
     * Check Web browser of client.
     *
     * @return string Web browser name.
     */
    public function checkWebBrowsers()
    {
        $sBrowser = t('Unknown Web Browser');

        foreach (self::$aWebBrowsers as $sRegex => $sName) {
            if ($this->find($sRegex, $this->sUserAgent)) {
                $sBrowser = $sName;
                break;
            }
        }

        return $sBrowser;
    }

    /**
     * Check Search engine bots.
     *
     * @return string Bot name found.
     */
    public function checkBots()
    {
        $sBot = t('Unknown Search Engine Bot');

        foreach (self::$aRobots as $sRegex => $sName) {
            if ($this->find($sRegex, $this->sUserAgent)) {
                $sBot = $sName;
                break;
            }
        }

        return $sBot;
    }

    /**
     * Check IP of search engine bots.
     *
     * @return string IP bot.
     */
    public function checkIpBots()
    {
        $sIpBot = t('Unknown Search Engine Bot IP');

        foreach (self::$aIpRobots as $sRegex => $sName) {
            if ($this->find($sRegex, $this->sUserAgent)) {
                $sIpBot = $sName;
                break;
            }
        }

        return $sIpBot;
    }

    /**
     * @return string|null Returns the keyword else if neither keyword is used, returns NULL
     */
    public function checkKeywords()
    {
        $sKeyword = null;

        foreach (self::$aKeywords as $sWord) {
            if ($this->find($sWord, $this->sReferer)) {
                $sKeyword = $sWord;
                break;
            }
        }

        return $sKeyword;
    }

    /**
     * Add data in the cache.
     *
     * @param string $sType File name.
     * @param string $sData
     *
     * @return void
     *
     * @throws IOException
     */
    public function add($sType, $sData)
    {
        $this->save($sType, $sData);
    }

    /**
     * Retrieve data cache.
     *
     * @param string $sFileName
     *
     * @return array Analytics data.
     *
     * @throws IOException
     */
    public function get($sFileName)
    {
        return $this->read($sFileName);
    }

    /**
     * Init method.
     *
     * @return void
     *
     * @throws IOException
     */
    private function init()
    {
        // Check and retrieve
        $sOs = $this->checkOs();
        $sWebBrowser = $this->checkWebBrowsers();
        $sBot = $this->checkBots();
        $sIpBot = $this->checkIpBots();
        $sKeyword = $this->checkKeywords();

        // Save
        $this->add('OS', $sOs);
        $this->add('WebBrowsers', $sWebBrowser);
        $this->add('Bots', $sBot);
        $this->add('IpBots', $sIpBot);
        $this->add('Keywords', $sKeyword);
        $this->add('UserLanguage', $this->sUserLang);
    }

    /**
     * Find a word in contents using the RegEx pattern.
     *
     * @param string $sToFind
     * @param string $sContents
     *
     * @return bool
     */
    private function find($sToFind, $sContents)
    {
        return preg_match('/' . $sToFind . '/i', $sContents);
    }
}
