<?php
/**
 * @title            2 Check Out Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Payment / Gateway / Api
 * @version          1.0
 */

namespace PH7\Framework\Payment\Gateway\Api;

defined('PH7') or exit('Restricted access');

class TwoCheckOut extends Provider implements Api
{
    /** @var string */
    private $sUrl = 'https://secure.2checkout.com/checkout/';

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bValid = false;


    /**
     * @param bool $bSandbox
     */
    public function __construct($bSandbox = false)
    {
        if ($bSandbox) {
            $this->param('demo', '1');
        }

        $this->param('mode', '2CO');
    }

    /**
     * Get Checkout URL.
     *
     * @param bool $bSinglePage TRUE = Single page, FALSE = Standard multi page.
     *
     * @return string
     */
    public function getUrl($bSinglePage = false)
    {
        $sPurchasePage = (true === (bool)$bSinglePage) ? 'spurchase' : 'purchase';

        return $this->sUrl . $sPurchasePage;
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
     * @param string $sVendorId
     * @param string $sSecretWord
     *
     * @return bool
     */
    public function valid($sVendorId = '', $sSecretWord = '')
    {
        // Instant Notification Service Messages
        $aInsMsg = [];

        foreach ($_POST as $sKey => $sVal) {
            $aInsMsg[$sKey] = $sVal;
        }

        if (
            !empty($_POST['message_type']) &&
            $_POST['message_type'] == 'FRAUD_STATUS_CHANGED'
        ) {
            $sHashPayload = $aInsMsg['sale_id'] . $sVendorId . $aInsMsg['invoice_id'] . $sSecretWord;
            $sLegacyHash = strtoupper(md5($sHashPayload));
            $sReceivedHash = strtoupper((string)($aInsMsg['md5_hash'] ?? $aInsMsg['hash'] ?? $aInsMsg['HASH'] ?? ''));

            if (
                ($aInsMsg['md5_hash'] ?? '') !== '' &&
                hash_equals($sLegacyHash, strtoupper((string)$aInsMsg['md5_hash']))
            ) {
                $this->bValid = true;
                $this->sMsg = t('Refund transaction valid.');
            } elseif (
                $sReceivedHash !== '' &&
                $this->validateShaSignature($sHashPayload, $sReceivedHash)
            ) {
                $this->bValid = true;
                $this->sMsg = t('Refund transaction valid.');
            } else {
                $this->bValid = false;
                $this->sMsg = t('Invalid refund transaction.');
            }
        } elseif (
            !empty($_REQUEST['key']) &&
            !empty($aInsMsg['order_number']) &&
            !empty($aInsMsg['total'])
        ) {
            $sHashPayload = $sSecretWord . $sVendorId . $aInsMsg['order_number'] . $aInsMsg['total'];
            $sLegacyHash = strtoupper(md5($sHashPayload));
            $sSha2Hash = strtoupper(hash('sha256', $sHashPayload));
            $sReceivedHash = strtoupper((string)($_REQUEST['key'] ?? ''));

            if ($sReceivedHash !== '' && ($sReceivedHash === $sLegacyHash || $sReceivedHash === $sSha2Hash)) {
                $this->bValid = true;
                $this->sMsg = t('Purchase transaction valid.');
            } else {
                $this->bValid = false;
                $this->sMsg = t('Invalid purchase transaction.');
            }
        } else {
            $this->bValid = false;
            $this->sMsg = t('Invalid connection to 2CheckOut.');
        }

        unset($aInsMsg);

        return $this->bValid;
    }

    private function validateShaSignature(string $sPayload, string $sReceivedHash): bool
    {
        [$sAlgorithm, $sHash] = $this->splitSignatureWithAlgorithm($sReceivedHash);

        if ($sAlgorithm !== null) {
            return $this->compareHash($sPayload, $sAlgorithm, $sHash);
        }

        // If no algorithm prefix is provided, try modern supported SHA variants.
        return $this->compareHash($sPayload, 'sha256', $sHash) ||
            $this->compareHash($sPayload, 'sha3-256', $sHash);
    }

    /**
     * @return array{0:?string,1:string}
     */
    private function splitSignatureWithAlgorithm(string $sReceivedHash): array
    {
        $sReceivedHash = strtoupper(trim($sReceivedHash));

        if (strpos($sReceivedHash, ':') === false) {
            return [null, $sReceivedHash];
        }

        [$sAlgorithm, $sHash] = explode(':', $sReceivedHash, 2);
        $sAlgorithm = strtolower(trim($sAlgorithm));
        $sHash = strtoupper(trim($sHash));

        $aAllowedAlgorithms = ['sha256', 'sha3-256'];
        if (!in_array($sAlgorithm, $aAllowedAlgorithms, true) || $sHash === '') {
            return [null, $sReceivedHash];
        }

        return [$sAlgorithm, $sHash];
    }

    private function compareHash(string $sPayload, string $sAlgorithm, string $sExpectedHash): bool
    {
        $sCalculatedHash = strtoupper(hash($sAlgorithm, $sPayload));

        return hash_equals($sCalculatedHash, $sExpectedHash);
    }
}
