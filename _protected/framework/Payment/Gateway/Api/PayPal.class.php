<?php

/**
 * @title            PayPal Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
 * @version          1.4
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

use PH7\Framework\File\Stream;

/**
 * PayPal class using PayPal's API.
 *
 * @see https://developer.paypal.com/api/nvp-soap/ipn/
 */
class PayPal extends Provider implements Api
{
    public const SANDBOX_PAYMENT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
    public const PAYMENT_URL = 'https://www.paypal.com/cgi-bin/webscr';
    public const SANDBOX_VERIFICATION_URL = 'https://ipnpb.sandbox.paypal.com/cgi-bin/webscr';
    public const VERIFICATION_URL = 'https://ipnpb.paypal.com/cgi-bin/webscr';

    /* Should we accept valid transactions but hasn't been completed yet? */
    public const ACCEPT_VALID_PAYMENT_NOT_COMPLETED = false;

    /** @var string */
    private $sUrl;

    private string $sVerificationUrl;

    /** @var string */
    private $sRequest = 'cmd=_notify-validate';

    /** @var string */
    private $sMsg;

    /** @var bool|null */
    private $bValid;

    protected bool $bTransportError = false;

    /**
     * @param bool $bSandbox Default FALSE
     */
    public function __construct($bSandbox = false)
    {
        if ($bSandbox) {
            $this->sUrl = self::SANDBOX_PAYMENT_URL;
            $this->sVerificationUrl = self::SANDBOX_VERIFICATION_URL;
        } else {
            $this->sUrl = self::PAYMENT_URL;
            $this->sVerificationUrl = self::VERIFICATION_URL;
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
     * @internal we added an empty parameter for the method only to be compatible with the API interface
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

    public function hasTransportError(): bool
    {
        return $this->bTransportError;
    }

    /**
     * Check if the transaction is valid.
     *
     * @param string $sParam1
     * @param string $sParam2
     *
     * @return bool
     *
     * @internal we added two empty parameters for the method only to be compatible with the API interface
     */
    public function valid($sParam1 = '', $sParam2 = '')
    {
        if ($this->isStatusAlreadyVerified()) {
            return $this->bValid;
        }

        $this->setParams();

        $mStatus = $this->getStatus();
        $mStatus = trim((string)$mStatus);

        if ($this->bTransportError) {
            $this->bValid = false;
            $this->sMsg = t('Connection to PayPal failed.');

            return false;
        }

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
            $this->bTransportError = true;
            $this->bValid = false;
            $this->sMsg = t('Connection to PayPal failed.');
        }

        return $this->bValid;
    }

    /**
     * Connect to PayPal.
     *
     * @return bool|string message from the transaction status on success or FALSE on failure
     */
    protected function getStatus()
    {
        $rCh = curl_init($this->sVerificationUrl);
        curl_setopt($rCh, CURLOPT_POST, 1);
        curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($rCh, CURLOPT_POSTFIELDS, $this->sRequest);
        curl_setopt($rCh, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($rCh, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($rCh, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($rCh, CURLOPT_TIMEOUT, 30);
        curl_setopt($rCh, CURLOPT_USERAGENT, 'pH7Builder PayPal IPN verifier');
        $sHost = (string)parse_url($this->sVerificationUrl, PHP_URL_HOST);
        if ($sHost !== '') {
            curl_setopt($rCh, CURLOPT_HTTPHEADER, [sprintf('Host: %s', $sHost)]);
        }
        $mRes = curl_exec($rCh);

        $iHttpStatus = (int)curl_getinfo($rCh, CURLINFO_HTTP_CODE);
        $this->bTransportError = $this->didTransportFail($mRes, curl_errno($rCh), $iHttpStatus);

        unset($rCh);

        return $mRes;
    }

    protected function getVerificationUrl(): string
    {
        return $this->sVerificationUrl;
    }

    protected function didTransportFail(mixed $mResponse, int $iCurlError, int $iHttpStatus): bool
    {
        return $mResponse === false
            || $iCurlError !== CURLE_OK
            || $iHttpStatus < 200
            || $iHttpStatus >= 300;
    }

    /**
     * Set the data parameters POST from PayPal system.
     *
     * @return self
     */
    protected function setParams()
    {
        $this->sRequest = $this->buildVerificationRequest((string)Stream::getInput());

        return $this;
    }

    protected function buildVerificationRequest(string $sRawPost): string
    {
        return $sRawPost === ''
            ? 'cmd=_notify-validate'
            : 'cmd=_notify-validate&' . $sRawPost;
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
        return isset($_POST['payment_status']) && $_POST['payment_status'] === 'Completed';
    }
}
