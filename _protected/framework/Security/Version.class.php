<?php
/**
 * @title            Version Class
 * @desc             Version Information for the security of packaged software.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security
 */

namespace PH7\Framework\Security;

defined('PH7') or exit('Restricted access');

use DOMDocument;
use DOMElement;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Security\Validate\Validate;

final class Version
{
    const CACHE_TIME = 86400; // 1 day
    const LATEST_VERSION_URL = 'http://xml.ph7cms.com/software-info.xml';
    const VERSION_PATTERN = '\d{1,2}\.\d{1,2}\.\d{1,2}';
    const FRAMEWORK_TAG_NAME = 'ph7';
    const PACKAGE_TAG_NAME = 'ph7builder';

    /**
     * Framework Kernel.
     *
     * VERSION NAMES:
     * 1.0, 1.1 branches were "pOH", 1.2 was "pOW", 1.3, 1.4 were "p[H]", 2.* was "H2O", 3.* was "H3O", 4.* was "HCO",
     * 5.* was "pCO", 6.* was "WoW", 7.*, 8.* were "NaOH", 10.* was "pKa", 12.* was "PHS" and 14.* is "pKb"
     */
    const KERNEL_VERSION_NAME = 'pKb';
    const KERNEL_VERSION = '14.0.0';
    const KERNEL_BUILD = '1';
    const KERNEL_RELEASE_DATE = '2018-08-20';

    /***** Framework Server *****/
    const KERNEL_TECHNOLOGY_NAME = 'pH7T/1.0.1'; // Ph7 Technology
    const KERNEL_SERVER_NAME = 'pH7WS/1.0.0'; // pH7 Web Server

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Gets information on the latest software version.
     *
     * @return array|bool Returns version information in an array or FALSE if an error occurred.
     */
    public static function getLatestInfo()
    {
        $oCache = (new Cache)->start('str/security', 'version-info', self::CACHE_TIME); // Stored for 1 day
        if (!$mData = $oCache->get()) {
            $oDom = new DOMDocument;

            if (!@$oDom->load(self::LATEST_VERSION_URL)) {
                return false;
            }

            /** @var DOMElement $oSoft */
            foreach ($oDom->getElementsByTagName(self::FRAMEWORK_TAG_NAME) as $oSoft) {
                // Get info for "ph7builder" package
                $oInfo = $oSoft->getElementsByTagName(self::PACKAGE_TAG_NAME)->item(0);

                $bIsAlert = self::isUpdateAlertEnabled($oInfo);
                $sVerName = $oInfo->getElementsByTagName('name')->item(0)->nodeValue;
                $sVerNumber = $oInfo->getElementsByTagName('version')->item(0)->nodeValue;
                $sVerBuild = $oInfo->getElementsByTagName('build')->item(0)->nodeValue;
            }
            unset($oDom);

            $mData = [
                'is_alert' => $bIsAlert,
                'name' => $sVerName,
                'version' => $sVerNumber,
                'build' => $sVerBuild
            ];
            $oCache->put($mData);
        }
        unset($oCache);

        return $mData;
    }

    /**
     * Checks if there is an update available.
     *
     * @return bool Returns TRUE if a new update is available, FALSE otherwise.
     */
    public static function isUpdateEligible()
    {
        if (!$aLatestInfo = self::getLatestInfo()) {
            return false;
        }

        $bIsAlert = $aLatestInfo['is_alert'];
        $sLastName = $aLatestInfo['name'];
        $sLastVer = $aLatestInfo['version'];
        $sLastBuild = $aLatestInfo['build'];
        unset($aLatestInfo);

        if (!$bIsAlert || !is_string($sLastName) || !preg_match('#^' . self::VERSION_PATTERN . '$#', $sLastVer)) {
            return false;
        }

        if (version_compare(self::KERNEL_VERSION, $sLastVer, '==')) {
            if (version_compare(self::KERNEL_BUILD, $sLastBuild, '<')) {
                return true;
            }
        }

        if (version_compare(self::KERNEL_VERSION, $sLastVer, '<')) {
            return true;
        }

        return false;
    }

    /**
     * @param DOMElement $oInfo
     *
     * @return bool
     */
    private static function isUpdateAlertEnabled(DOMElement $oInfo)
    {
        // "Validate::bool()" returns TRUE for "1", "true", "on" and "yes", FALSE otherwise
        return (new Validate)->bool($oInfo->getElementsByTagName('upd-alert')->item(0)->nodeValue);
    }
}
