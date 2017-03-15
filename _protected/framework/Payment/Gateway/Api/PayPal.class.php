<?php
/**
 * @title            PayPal Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.1
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Stream;
use PH7\Framework\Url\Url;

/**
 * PayPal class using PayPal's API
 *
 * @link https://developer.paypal.com/docs/integration/direct/identity/seamless-checkout/
 */
class Paypal extends Provider implements Api
{
    const SANDBOX_PAYMENT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    const PAYMENT_URL = 'https://www.paypal.com/cgi-bin/webscr';

    /* Should we accept valid transactions but hasn't been completed yet? */
    const ACCEPT_VALID_PAYMENT_NOT_COMPLETED = true;

    /** @var string */
    private $_sUrl;

    /** @var string */
    private $_sRequest = 'cmd=_notify-validate';

    /** @var string */
    private $_sMsg;

    /** @var bool|null */
    private $_bValid = null;


    /**
     * @param boolean $bSandbox Default FALSE
     */
    public function __construct($bSandbox = false)
    {
        if ($bSandbox) {
            $this->_sUrl = self::SANDBOX_PAYMENT_URL;
        } else {
            $this->_sUrl = self::PAYMENT_URL;
        }

        $this->param('cmd', '_xclick');
    }

    /**
     * Get Checkout URL.
     *
     * @param string $sParam
     *
     * @return string
     * @internal We added an empty parameter for the method only to be compatible with the API interface.
     */
    public function getUrl($sParam = '')
    {
        return $this->_sUrl;
    }

    /**
     * Get message status.
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->_sMsg;
    }

    /**
     * Check if the transaction is valid.
     *
     * @param string $sParam1
     * @param string $sParam2
     *
     * @return boolean
     * @internal We added two empty parameters for the method only to be compatible with the API interface.
     */
    public function valid($sParam1 = '', $sParam2 = '')
    {
        // If already validated, just return last result
        if (true === $this->_bValid || false === $this->_bValid) {
            return $this->_bValid;
        }

        $this->setParams();

        $mStatus = $this->getStatus();
        $mStatus = trim($mStatus);

        if (0 === strcmp('VERIFIED', $mStatus)) {
            // Valid
            if ($_POST['payment_status'] == 'Completed') {
                $this->_bValid = true;
                $this->_sMsg = t('Transaction valid and completed.');
            } else {
                $this->_bValid = self::ACCEPT_VALID_PAYMENT_NOT_COMPLETED;
                $this->_sMsg = t('Transaction valid but not completed.');
            }
        } elseif (0 === strcmp('INVALID', $mStatus)) {
            // Bad Connection
            $this->_bValid = false;
            $this->_sMsg = t('Invalid transaction.');
        } else {
            // Bad Connection
            $this->_bValid = false;
            $this->_sMsg = t('Connection to PayPal failed.');
        }

        return $this->_bValid;
    }

    /**
     * Connect to Paypal.
     *
     * @return boolean|string Message from the transaction status on success or FALSE on failure.
     */
     protected function getStatus()
     {
         $rCh = curl_init($this->_sUrl);
         curl_setopt($rCh, CURLOPT_POST, 1);
         curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($rCh, CURLOPT_POSTFIELDS, $this->_sRequest);
         curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, 1);
         curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, 2);
         curl_setopt($rCh, CURLOPT_HTTPHEADER, array('Host: www.paypal.com'));
         $mRes = curl_exec($rCh);

         if (curl_errno($rCh) == 60) {
             // CURLE_SSL_CACERT
             curl_setopt($rCh, CURLOPT_CAINFO, __DIR__ . '/cert/paypal_api_chain.crt');
             $mRes = curl_exec($rCh);
         }

         curl_close($rCh);
         unset($rCh);

         return $mRes;
     }

    /**
     * Set the data parameters POST from PayPal system.
     *
     * @return self
     */
     protected function setParams()
     {
         foreach ($this->getPostDatas() as $sKey => $sValue) {
             $this->setUrlData($sKey, $sValue);
         }

         return $this;
     }

    /**
     * Set data URL e.g., "&key=value"
     *
     * @param string $sName
     * @param string $sValue
     *
     * @return self
     */
    protected function setUrlData($sName, $sValue)
    {
        $this->_sRequest .= '&' . $sName . '=' . Url::encode($sValue);
        return $this;
    }

    /**
     * Get the Post Data.
     *
     * @return array
     */
    protected function getPostDatas()
    {
        $rRawPost = Stream::getInput();
        $aRawPost = explode('&', $rRawPost);
        $aPostData = array();

        foreach ($aRawPost as $sKeyVal) {
            $aKeyVal = explode ('=', $sKeyVal);
            if (count($aKeyVal) == 2) {
                $aPostData[$aKeyVal[0]] = Url::decode($aKeyVal[1]);
            }
        }
        unset($aRawPost);

        return $aPostData;
    }
}
