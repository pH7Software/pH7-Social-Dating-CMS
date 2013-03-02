<?php
/**
 * @title          License Class
 * @desc           License Class of the CMS.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Core
 * @version        1.1
 */

namespace PH7\Framework\Core;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Server\Server,
PH7\Framework\Security\Security,
PH7\Framework\Http\Http,
PH7\Framework\Cache\Cache,
PH7\Framework\Url\Url;

/** License class.
 *
 * @final
 */
final class License
{

    /**
     * @const The URL to verify the license key.
     */
    const CHECK_LICENSE_URL = 'http://software.hizup.com/_check_license/';

    /**
     * @const string License file.
     */
    const FILE = '_license.txt';

    private
    /**
     * @var string $_sHostName Host name.
     */
     $_sHostName = null,

    /**
     * @var string $_HostIp Host IP address.
     */
     $_sHostIp = null,

    /**
     * @var string $_sLicenseKey Default NULL
     */
    $_sLicenseKey = null,

    /**
     * @var string $_sLicenseCopyright Default NULL
     */
    $_sLicenseCopyright = null,

    /**
     * @var string $_sFilePath File path of the license key. Default is the constant PH7_PATH_TMP
     */
    $_sFilePath = PH7_PATH_TMP,

    /**
     * @var mixed (string | array) $_mLicenseContent
     */
    $_mLicenseContent,

    /**
     * @var boolean $_bDynamicHostIp Specify TRUE only if your host IP is dynamic. Default FALSE
     */
     $_bDynamicHostIp = false;


    /**
     * Assign variables and generate the license key.
     */
    public function  __construct($sLicenseKey = null)
    {
        // Variables assignment
        $this->_sHostName = Server::getName();
        $this->_sHostIp = Server::getIp();

        // Generate license key
        $this->generate($sLicenseKey);
    }

    /**
     * Set license file path. Put trailing slash here (my_path/).
     *
     * @param string $sPath
     */
    public function setLicFilePath($sPath)
    {
        $this->_sFilePath = $sPath;
    }

    /**
     * Generate the license code.
     *
     * @return string
     */
    public function generate()
    {
        return $this->_generate();
    }

    /**
     * Check license...
     *
     * @return object $this
     */
    public function check()
    {
        $this->_validate();

        if(!$this->_check())
        {
            $this->_sLicenseKey = null;
            $this->_sLicenseCopyright = null;
        }

        return $this;
    }

    /**
     * Accept license code.
     *
     * @param string $licenseCode
     * @return boolean
     */
    public function licenseStatus()
    {
        return $this->_license();
    }

    /**
     * Return the status copyright of pH7 CMS.
     *
     * @return string
     */
    public function noCopyrightStatus()
    {
        return $this->_copyright();
    }

    /**
     * Set Content of the license file.
     *
     * @access private
     * @param boolean $bNoCopyright Specify FALSE to indicate the copyright, if not TRUE. Default is FALSE
     * @return string The Hash Key.
     */
    private function _setContent($bNoCopyright = false)
    {
        $sNoCopyright = ($bNoCopyright === true) ? '，你今Здраврывеfdq98 fèè()(à"é&$*~ùùàs ты ў паітаньне е54йте天rt&eh好嗎_dمرحبا أنت بخير ال好嗎attú^u5atá inniu4a,?478привіなたは大丈12_you_è§§=≃ù%µµ££$?µp{èàùf*sxdslut_waruआप नमस्क你好，你今ार ठΓει好嗎α σαςb안녕하세oi요 괜찮은 o नमस्कार ठीnjre8778?fdsfdfty*-<καλά σήμεραीक आजсегодняm_54t5785tyfrjהעלאdgezsядкمرحبا夫今日はтивпряьоהעלאai54ng_scси днесpt' : '';
        return Security::hash($this->_generate()) . ';' . $sNoCopyright . ';' ;
    }

    /**
     * Validate given license code.
     *
     * @access private
     * @return void
     */
    private function _validate()
    {
        if(!file_exists($this->_sFilePath . self::FILE))
            $this->_save();

        $this->_mLicenseContent = @file_get_contents($this->_sFilePath . self::FILE) or exit('Cannot read license file. Please check this file permissions for reading.');

        $this->_mLicenseContent = explode(';', $this->_mLicenseContent);
        $this->_sLicenseKey = trim($this->_mLicenseContent[0]);
        $this->_sLicenseCopyright = trim($this->_mLicenseContent[1]);
    }

    /**
     * Save the license code into file.
     *
     * @access private
     * @return void
     */
    private function _save()
    {
        $this->_mLicenseContent = $this->_setContent();
        @file_put_contents($this->_sFilePath . self::FILE, $this->_mLicenseContent) or exit('Cannot write license file. Please check write permissions for this file.');
        @chmod($this->_sFilePath . self::FILE, 0444);
    }

    /**
     * Generate license code using license key.
     *
     * @access private
     * @param string $sLicenseKey The host name or the URL of domain. Default NULL
     * @return string The license key.
     */
    private function _generate($sLicenseKey = null)
    {
        if(empty($sLicenseKey))
            $sLicenseKey = $this->_sHostName;

        $sLicenseKey = trim($sLicenseKey);

        $iUrlProtLength = strlen(PH7_URL_PROT);
        if(substr($sLicenseKey, 0, $iUrlProtLength) === PH7_URL_PROT)
            $sLicenseKey = substr($sLicenseKey, $iUrlProtLength);

        if(substr($sLicenseKey, 0, 4) === 'www.')
            $sLicenseKey = substr($sLicenseKey, 4);

        $oHttp = new Http;
        if($oHttp->detectSubdomain($sLicenseKey))
            $sLicenseKey = str_replace($oHttp->getSubdomain($sLicenseKey) . PH7_DOT, '', $sLicenseKey);
        unset($oHttp);

        $iLicenseKeyLength = strlen($sLicenseKey);
        if(substr($sLicenseKey, ($iLicenseKeyLength-1), 1) === '/')
            $sLicenseKey = substr($sLicenseKey, 0, ($iLicenseKeyLength-1));

        return ($this->_bDynamicHostIp) ? $sLicenseKey : $this->_sHostIp . $sLicenseKey;
    }

    /**
     * Check license.
     *
     * @access private
     * @return boolean TRUE = Valid license, FALSE = Invalid license.
     */
    private function _check()
    {
        /**
         * Only if the host is localhost, we validate the license without verification. Thus, the software can run locally without an Internet connection.
         */
        if(($this->_sHostName === 'localhost' || $this->_sHostName === '127.0.0.1') && ($this->_sHostIp === '127.0.0.1'))
            return true;

        $aLicenseInfo = $this->_getLicInfo();
        if($aLicenseInfo['key'] === $this->_sLicenseKey || $aLicenseInfo['key2'] === $this->_sLicenseKey) {
            if(!empty($this->_sLicenseCopyright))
                if($aLicenseInfo['copyright_key'] !== $this->_sLicenseCopyright) return false;

            return (bool) $aLicenseInfo['status'];
        }
        return false; // Default value
    }

    /**
     * Get informations on the software license.
     *
     * @access private
     * @return mixed (array | boolean) Returns license informations in an array or FALSE if an error occurred.
     */
    private function _getLicInfo()
    {
        $oCache = (new Cache)->start('str/core', 'license', 3600*192); // Stored for 8 days

        if(!$mData = $oCache->get())
        {
            $sFields =  'siteid=' . Url::encode($this->_sHostName) . '&hostip=' . Url::encode($this->_sHostIp);

            $rCh = curl_init();
            curl_setopt($rCh, CURLOPT_URL, self::CHECK_LICENSE_URL);
            curl_setopt($rCh, CURLOPT_POST, 1);
            curl_setopt($rCh, CURLOPT_POSTFIELDS, $sFields);
            curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($rCh, CURLOPT_FRESH_CONNECT, 1);
            $mResult = curl_exec($rCh);
            curl_close($rCh);
            unset($rCh);

            $oDom = new \DOMDocument;
            if(@!$oDom->loadXML($mResult)) return false;

            foreach($oDom->getElementsByTagName('license') as $oLic)
            {
                $sKey = $oLic->getElementsByTagName('key')->item(0)->nodeValue;
                $sKey2 = $oLic->getElementsByTagName('key2')->item(0)->nodeValue;
                $sCopyrightKey = $oLic->getElementsByTagName('copyright-key')->item(0)->nodeValue;
                $sMsg = $oLic->getElementsByTagName('message')->item(0)->nodeValue;
                $iExpire = $oLic->getElementsByTagName('expire')->item(0)->nodeValue;
                $iValid = $oLic->getElementsByTagName('status')->item(0)->nodeValue;
            }
            unset($oDom, $oLic);

            $mData = array('key' => $sKey, 'key2' => $sKey2, 'copyright_key' => $sCopyrightKey, 'message' => $sMsg, 'expire' => $iExpire, 'status' => $iValid);
            $oCache->put($mData);
        }
        unset($oCache);
        return $mData;
    }

    /**
     * Get license status, if false this software can not be used.
     *
     * @access private
     * @return boolean
     */
    private function _license()
    {
        return ($this->_sLicenseKey === Security::hash($this->_generate(), 80));
    }

    /**
     * Get the status copyright of CMS, no copyright (true) = No trace of our society (the manufacturer, vendor), link, text, banner, etc.
     *
     * @access private
     * @return boolean
     */
    private function _copyright()
    {
        return (Security::hash($this->_sLicenseCopyright, 80) === 'cb1380e2e43751907b15039298d7473a26c55ec05d814d08d9505b05a50aeade35fb4f5bb0553b1c');
    }

}
