<?php
/**
 * @desc             Version Information for the security of packaged software.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Security
 */

declare(strict_types=1);

namespace PH7\Framework\Security;

defined('PH7') or exit('Restricted access');

use DOMDocument;
use DOMElement;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Security\Validate\Validate;

final class Version
{
    private const CACHE_GROUP = 'str/security';
    private const CACHE_TIME = 86400; // Cache lifetime set for 1 day

    private const LATEST_VERSION_URL = 'https://xml.ph7builder.com/software-info.xml';

    public const UPGRADE_DOC_URL = 'https://ph7builder.com/doc/en/upgrade';

    public const VERSION_PATTERN = '\d{1,2}\.\d{1,2}\.\d{1,2}';

    private const FRAMEWORK_TAG_NAME = 'ph7';
    private const PACKAGE_TAG_NAME = 'ph7builder';

    /**
     * Framework Kernel Information.
     *
     * @history VERSION NAMES:
     *
     * 1.0, 1.1 branches were "pOH", 1.2 was "pOW", 1.3, 1.4 were "p[H]", v2.* was "H2O",
     * v3.* was "H3O", v4.* was "HCO", v5.* was "pCO", v6.* was "WoW",
     * v7.* and v8.* were "NaOH", v10.* was "pKa", v12.* was "PHS", v14.* was "pKb",
     * v15.* was ABSOLUTE™, v16.* was ACIDIC, v17.* was PURE™ and v18 is SENSATION
     */
    public const KERNEL_VERSION_NAME = 'SENSATION';

    /**
     * VERSION NUMBERS:
     * MAJOR.MINOR.PATCH[.build]
     *
     * More details: https://ph7builder.com/new-versioning-system/
     */
    public const KERNEL_VERSION = '18.0.0';
    public const KERNEL_BUILD = '1';
    public const KERNEL_RELEASE_DATE = '2023-01-02';

    /*** Framework Server ***/
    public const KERNEL_TECHNOLOGY_NAME = 'pH7Builder.com';
    public const KERNEL_SERVER_NAME = 'pH7WS/1.0.0';

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
    public static function getLatestInfo(): array|bool
    {
        $oCache = (new Cache)->start(self::CACHE_GROUP, 'version-info', self::CACHE_TIME);
        if (!$mData = $oCache->get()) {
            $mData = self::retrieveXmlInfoFromRemoteServer();
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
    public static function isUpdateEligible(): bool
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
     * @return array|bool Returns an array with the release details, or FALSE if it can't retrieve the remote info.
     */
    private static function retrieveXmlInfoFromRemoteServer(): array|bool
    {
        $oDom = new DOMDocument;

        if (!@$oDom->load(self::LATEST_VERSION_URL)) {
            return false;
        }

        // Set default values to variables (if nothing in foreach loop)
        $bIsAlert = $sVerName = $sVerNumber = $sVerBuild = null;

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

        return [
            'is_alert' => $bIsAlert,
            'name' => $sVerName,
            'version' => $sVerNumber,
            'build' => $sVerBuild
        ];
    }

    private static function isUpdateAlertEnabled(DOMElement $oInfo): bool
    {
        // "Validate::bool()" returns TRUE for "1", "true", "on", and "yes", FALSE otherwise
        return (new Validate)->bool($oInfo->getElementsByTagName('upd-alert')->item(0)->nodeValue);
    }
}
