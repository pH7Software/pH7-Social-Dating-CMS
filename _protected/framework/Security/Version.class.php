<?php
/**
 * @title            Version Class
 * @desc             Version Information for the security of packaged software.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Security
 * @version          1.0
 */

namespace PH7\Framework\Security;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel, PH7\Framework\Security\Validate\Validate;

final class Version
{

    const LATEST_VERSION_URL = 'http://ph7cms.com/xml/software-info.xml', PATTERN = '\d{1,2}\.\d{1,2}\.\d{1,2}';

    /***** Framework Kernel *****/
    const KERNEL_VERSION_NAME = 'pOW'; // 1.0 and 1.1 branches was "pOH", 1.2 branch is now "pOW" and the new one should be "p[H]"
    const KERNEL_VERSION = '1.2.3';
    const KERNEL_BUILD = '1';
    const KERNEL_RELASE_DATE = '2015-09-08';

    /***** Framework Server *****/
    const KERNEL_TECHNOLOGY_NAME = 'pH7T/1.0.1'; // Ph7 Technology
    const KERNEL_SERVER_NAME = 'pH7WS/1.0.0'; // pH7 Web Server

    /***** Form PFBC *****/
    const PFBC_VERSION = '2.3';
    const PFBC_RELASE_DATE = '2011-09-22';


    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Gets information on the lastest software version.
     *
     * @return mixed (array | boolean) Returns version information in an array or FALSE if an error occurred.
     */
    public static function getLatestInfo()
    {
        $oCache = (new \PH7\Framework\Cache\Cache)->start('str/security', 'version-info', 3600*24); // Stored for 1 day
        if(!$mData = $oCache->get())
        {
            $oDom = new \DOMDocument;
            if(!@$oDom->load(self::LATEST_VERSION_URL)) return false;

            foreach($oDom->getElementsByTagName('ph7') as $oSoft)
            {
                foreach($oSoft->getElementsByTagName('social-dating-cms') as $oInfo)
                {
                    $bIsAlert = (new Validate)->bool($oInfo->getElementsByTagName('upd-alert')->item(0)->nodeValue);
                    $sVerName = $oInfo->getElementsByTagName('name')->item(0)->nodeValue;
                    $sVerNumber = $oInfo->getElementsByTagName('version')->item(0)->nodeValue;
                    $sVerBuild = $oInfo->getElementsByTagName('build')->item(0)->nodeValue;
                }
            }
            unset($oDom);

            $mData = array('is_alert' => $bIsAlert, 'name' => $sVerName, 'version' => $sVerNumber, 'build' => $sVerBuild);
            $oCache->put($mData);
        }
        unset($oCache);

        return $mData;
    }

    /**
     * Checks if there is an update available.
     *
     * @return boolean Returns TRUE if a new update is available, FALSE otherwise.
     */
    public static function isUpdates()
    {
        if(!$aLatestInfo = self::getLatestInfo()) return false;

        $bIsAlert = $aLatestInfo['is_alert'];
        $sLastName = $aLatestInfo['name'];
        $sLastVer = $aLatestInfo['version'];
        $sLastBuild = $aLatestInfo['build'];
        unset($aLatestInfo);

        if(!$bIsAlert || !is_string($sLastName) || !preg_match('#^' . self::PATTERN . '$#', $sLastVer)) return false;

        if(version_compare(Kernel::SOFTWARE_VERSION, $sLastVer, '=='))
        {
            if(version_compare(Kernel::SOFTWARE_BUILD, $sLastBuild, '<'))
                return true;
        }
        else
        {
            if(version_compare(Kernel::SOFTWARE_VERSION, $sLastVer, '<'))
                return true;
        }
        return false;
    }

}
