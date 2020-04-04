<?php
/**
 * @title            PayPal Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.4
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
class PayPal extends Provider implements Api
{
    const SANDBOX_PAYMENT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    const PAYMENT_URL = 'https://www.paypal.com/cgi-bin/webscr';
    const PAYPAL_HOST = 'www.paypal.com';

    /* Should we accept valid transactions but hasn't been completed yet? */
    const ACCEPT_VALID_PAYMENT_NOT_COMPLETED = true;

    /** @var string */
    private $sUrl;

    /** @var string */
    private $sRequest = 'cmd=_notify-validate';

    /** @var string */
    private $sMsg;

    /** @var bool|null */
    private $bValid = null;


    /**
     * @param bool $bSandbox Default FALSE
     */
    public function __construct($bSandbox = false)
    {
        if ($bSandbox) {
            $this->sUrl = self::SANDBOX_PAYMENT_URL;
        } else {
            $this->sUrl = self::PAYMENT_URL;
        }

        $this->param('cmd', '_xclick');
    }

    /**
     * Get Checkout URL.
     *
     * @param string $sParam
     *
     * @return string
     *
     * @internal We added an empty parameter for the method only to be compatible with the API interface.
     */
    public function getUrl($sParam = '')
    {
        return $this->sUrl;
    }

    /**
     * Get message status.
     *
     * @return string
     */
    public function getMsg()
    {
        return $this->sMsg;
    }

    /**
     * Check if the transaction is valid.
     *
     * @param string $sParam1
     * @param string $sParam2
     *
     * @return bool
     *
     * @internal We added two empty parameters for the method only to be compatible with the API interface.
     */
    public function valid($sParam1 = '', $sParam2 = '')
    {
        if ($this->isStatusAlreadyVerified()) {
            return $this->bValid;
        }

        $this->setParams();

        $mStatus = $this->getStatus();
        $mStatus = trim($mStatus);

        if (0 === strcmp('VERIFIED', $mStatus)) {
            if ($this->isValidPayment()) {
                $this->bValid = true;
                $this->sMsg = t('Transaction valid and completed.');
            } else {
                $this->bValid = self::ACCEPT_VALID_PAYMENT_NOT_COMPLETED;
                $this->sMsg = t('Transaction valid but not completed.');
            }
        } elseif (0 === strcmp('INVALID', $mStatus)) {
            $this->bValid = false;
            $this->sMsg = t('Invalid transaction.');
        } else {
            $this->bValid = false;
            $this->sMsg = t('Connection to PayPal failed.');
        }

        return $this->bValid;
    }

    /**
     * Connect to PayPal.
     *
     * @return bool|string Message from the transaction status on success or FALSE on failure.
     */
    protected function getStatus()
    {
        $rCh = curl_init($this->sUrl);
        curl_setopt($rCh, CURLOPT_POST, 1);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($rCh, CURLOPT_POSTFIELDS, $this->sRequest);
        curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($rCh, CURLOPT_HTTPHEADER, [sprintf('Host: %s', self::PAYPAL_HOST)]);
        $mRes = curl_exec($rCh);

        if (curl_errno($rCh) === 60) {
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
        foreach ($this->getPostData() as $sKey => $sValue) {
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
        $this->sRequest .= '&' . $sName . '=' . Url::encode($sValue);

        return $this;
    }

    /**
     * Get the Post Data.
     *
     * @return array
     */
    protected function getPostData()
    {
        $rRawPost = Stream::getInput();
        $aRawPost = explode('&', $rRawPost);
        $aPostData = [];

        foreach ($aRawPost as $sKeyVal) {
            $aKeyVal = explode('=', $sKeyVal);
            if (count($aKeyVal) === 2) {
                $aPostData[$aKeyVal[0]] = Url::decode($aKeyVal[1]);
            }
        }
        unset($aRawPost);

        return $aPostData;
    }

    /**
     * @return bool
     */
    private function isStatusAlreadyVerified()
    {
        return $this->bValid === true || $this->bValid === false;
    }

    /**
     * @return bool
     */
    private function isValidPayment()
    {
        return $_POST['payment_status'] === 'Completed';
    }
}
