<?php
/**
 * @title          License Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Core
 * @version        1.1
 */

namespace PH7\Framework\Core;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\License as LicenseModel,
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

    private
    /**
     * @var object $_oLicenseModel License object.
     */
     $_oLicenseModel,

    /**
     * @var string $_sBasicLicKey The basic license key automatically generated.
     */
     $_sBasicLicKey,
    /**
     * @var string $_sHostName Host name.
     */
     $_sHostName,

    /**
     * @var string $_HostIp Host IP address.
     */
     $_sHostIp,

    /**
     * @var string $_sLicenseKey Default NULL
     */
    $_sLicKey = null,

    /**
     * @var string $_sLicCopyright Default NULL
     */
    $_sLicCopyright = null,

    /**
     * @var mixed (string | array) $_mLicContent
     */
    $_mLicContent,

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
        $this->_oLicenseModel = new LicenseModel;
        $this->_sHostName = Server::getName();
        $this->_sHostIp = Server::getIp();
        $this->_sBasicLicKey = Security::hash($this->_generate(), 80);

        // Generate license key
        $this->generate($sLicenseKey);
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

        if (!$this->_check())
        {
            $this->_sLicKey = null;
            $this->_sLicCopyright = null;
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
     * Get the license content.
     *
     * @access private
     * @return string The Hash Key.
     */
    private function _getContent()
    {
        return $this->_sBasicLicKey . ';;' ;
    }

    /**
     * Validate given license code.
     *
     * @access private
     * @return void
     */
    private function _validate()
    {
        // If there is no license key saved yet, it'll put a default license key.
        if (trim($this->_oLicenseModel->get()) == '')
            $this->_save();

        $this->_mLicContent = $this->_oLicenseModel->get();

        $this->_mLicContent = explode(';', $this->_mLicContent);
        $this->_sLicKey = trim($this->_mLicContent[0]);
        $this->_sLicCopyright = trim($this->_mLicContent[1]);

        // If the basic license is wrong, it'll try to generate a new.
        if (!$this->licenseStatus())
            $this->_regenerateKey();
    }

    /**
     * Save the license code into the database.
     *
     * @access private
     * @return void
     */
    private function _save()
    {
        $this->_mLicContent = $this->_getContent();
        $this->_oLicenseModel->save($this->_mLicContent);
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
        if (empty($sLicenseKey))
            $sLicenseKey = $this->_sHostName;

        $sLicenseKey = trim($sLicenseKey);

        $iUrlProtLength = strlen(PH7_URL_PROT);
        if (substr($sLicenseKey, 0, $iUrlProtLength) === PH7_URL_PROT)
            $sLicenseKey = substr($sLicenseKey, $iUrlProtLength);

        if (substr($sLicenseKey, 0, 4) === 'www.')
            $sLicenseKey = substr($sLicenseKey, 4);

        $oHttp = new Http;
        if ($oHttp->detectSubdomain($sLicenseKey))
            $sLicenseKey = str_replace($oHttp->getSubdomain($sLicenseKey) . PH7_DOT, '', $sLicenseKey);
        unset($oHttp);

        $iLicenseKeyLength = strlen($sLicenseKey);
        if (substr($sLicenseKey, ($iLicenseKeyLength-1), 1) === PH7_SH)
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
         * Only if the host is localhost, it validate the license without verification. Thus, the software can run locally without an Internet connection.
         */
        if (($this->_sHostName === 'localhost' || $this->_sHostName === '127.0.0.1') && ($this->_sHostIp === '127.0.0.1'))
            return true;

        $aLicenseInfo = $this->_getLicInfo();
        if ($aLicenseInfo['key'] === $this->_sLicKey || $aLicenseInfo['key2'] === $this->_sLicKey)
        {
            if (!empty($this->_sLicCopyright))
                if ($aLicenseInfo['copyright_key'] !== $this->_sLicCopyright) return false;

            return (bool) $aLicenseInfo['status'];
        }
        return false; // Default value
    }

    /**
     * Get information on the software license.
     *
     * @access private
     * @return mixed (array | boolean) Returns license information in an array or FALSE if an error occurred.
     */
    private function _getLicInfo()
    {
        $oCache = (new Cache)->start('str/core', 'license', 3600*192); // Stored for 8 days
        if (!$mData = $oCache->get())
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
            if (@!$oDom->loadXML($mResult)) return false;

            foreach ($oDom->getElementsByTagName('license') as $oLic)
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
        return ($this->_sLicKey === $this->_sBasicLicKey);
    }

    /**
     * Get the status copyright, no copyright (true) = No trace of our society (the manufacturer, vendor), link, text, banner, etc.
     *
     * @access private
     * @return boolean
     */
    private function _copyright()
    {
        return (Security::hash($this->_sLicCopyright, 80) === 'cb1380e2e43751907b15039298d7473a26c55ec05d814d08d9505b05a50aeade35fb4f5bb0553b1c');
    }

    /**
     * Regenerate a license key if the basic key is wrong.
     *
     * @return void
     */
    private function _regenerateKey()
    {
        // Update the '_sLicKey' property.
        $this->_sLicKey = $this->_sBasicLicKey;

        // Save the new basic key in the database.
        $this->_oLicenseModel->save($this->_sLicKey . ';' . $this->_sLicCopyright . ';');
    }

}
