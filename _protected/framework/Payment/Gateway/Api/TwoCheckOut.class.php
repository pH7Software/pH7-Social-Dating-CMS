<?php

/**
 * @title            2 Check Out Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 *
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
     * @param bool $bSinglePage TRUE = Single page, FALSE = Standard multi page
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
     * @param string $sSecretKey
     *
     * @return bool
     */
    public function valid($sVendorId = '', $sSecretWord = '', $sSecretKey = '')
    {
        // Instant Notification Service Messages
        $aInsMsg = $_POST;
        $aReturnData = $_GET;

        if (
            ($aInsMsg['message_type'] ?? '') === 'FRAUD_STATUS_CHANGED'
            && $this->hasScalarValues($aInsMsg, ['sale_id', 'invoice_id', 'fraud_status'])
        ) {
            $sHashPayload = $aInsMsg['sale_id'] . $sVendorId . $aInsMsg['invoice_id'] . $sSecretWord;
            $sLegacyHash = strtoupper(md5($sHashPayload));
            $sLegacyReceivedHash = strtoupper((string)($aInsMsg['md5_hash'] ?? ''));
            $sModernReceivedHash = (string)($aInsMsg['hash'] ?? $aInsMsg['HASH'] ?? '');
            $bSignatureValid = (
                $sLegacyReceivedHash !== ''
                && hash_equals($sLegacyHash, $sLegacyReceivedHash)
            ) || (
                $sModernReceivedHash !== ''
                && $sSecretKey !== ''
                && $this->validateHmacSignature($sHashPayload, $sModernReceivedHash, $sSecretKey)
            );

            if ($bSignatureValid && ($aInsMsg['fraud_status'] ?? '') === 'pass') {
                $this->bValid = true;
                $this->sMsg = t('Transaction valid and completed.');
            } else {
                $this->bValid = false;
                $this->sMsg = t('Invalid transaction.');
            }
        } elseif (
            $this->hasScalarValues($aReturnData, ['key', 'order_number', 'total'])
        ) {
            $sHashPayload = $sSecretWord . $sVendorId . $aReturnData['order_number'] . $aReturnData['total'];
            $sLegacyHash = strtoupper(md5($sHashPayload));
            $sReceivedHash = strtoupper((string)$aReturnData['key']);

            if ($sReceivedHash !== '' && hash_equals($sLegacyHash, $sReceivedHash)) {
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

        unset($aInsMsg, $aReturnData);

        return $this->bValid;
    }

    private function validateHmacSignature(
        string $sPayload,
        string $sReceivedHash,
        string $sSecretKey
    ): bool {
        [$sAlgorithm, $sHash] = $this->splitSignatureWithAlgorithm($sReceivedHash);

        if ($sAlgorithm === null) {
            return false;
        }

        $sCalculatedHash = strtoupper(hash_hmac($sAlgorithm, $sPayload, $sSecretKey));

        return hash_equals($sCalculatedHash, $sHash);
    }

    /**
     * @param string[] $aFieldNames
     */
    private function hasScalarValues(array $aData, array $aFieldNames): bool
    {
        foreach ($aFieldNames as $sFieldName) {
            if (
                !isset($aData[$sFieldName])
                || !is_scalar($aData[$sFieldName])
                || is_bool($aData[$sFieldName])
                || trim((string)$aData[$sFieldName]) === ''
            ) {
                return false;
            }
        }

        return true;
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
}
