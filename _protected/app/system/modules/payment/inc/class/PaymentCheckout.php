<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

final class PaymentCheckout
{
    private const TOKEN_NAME_PREFIX = 'payment_checkout_';
    private const CONTEXT_NAME_PREFIX = 'payment_checkout_context_';
    private const PAYPAL_CONTEXT_NAME_PREFIX = 'paypal_checkout_reference_';
    private const PAYPAL_REFERENCE_PATTERN = '/^[a-f0-9]{40}$/';
    private const PRICE_SCALE = 100;
    private const VAT_SCALE = 1000;
    private const PERCENT_SCALE = 100 * self::VAT_SCALE;

    private function __construct()
    {
    }

    public static function createPayPalReference(): string
    {
        return bin2hex(random_bytes(20));
    }

    public static function isPayPalReference(string $sReference): bool
    {
        return preg_match(self::PAYPAL_REFERENCE_PATTERN, $sReference) === 1;
    }

    public static function getTokenName(int $iMembershipId): string
    {
        return self::TOKEN_NAME_PREFIX . $iMembershipId;
    }

    public static function getContextName(int $iMembershipId): string
    {
        return self::CONTEXT_NAME_PREFIX . $iMembershipId;
    }

    public static function getPayPalContextName(int $iMembershipId): string
    {
        return self::PAYPAL_CONTEXT_NAME_PREFIX . $iMembershipId;
    }

    public static function createReference(int $iMembershipId, string $sToken): string
    {
        if ($iMembershipId < 1 || !self::isValidToken($sToken)) {
            throw new \InvalidArgumentException('A valid membership and checkout token are required.');
        }

        return rtrim(strtr(base64_encode($iMembershipId . '|' . $sToken), '+/', '-_'), '=');
    }

    /**
     * @return array{membership_id:int,token:string}|null
     */
    public static function parseReference(string $sReference): ?array
    {
        if ($sReference === '' || preg_match('/^[A-Za-z0-9_-]+$/', $sReference) !== 1) {
            return null;
        }

        $iPadding = (4 - strlen($sReference) % 4) % 4;
        $sDecodedReference = base64_decode(
            strtr($sReference, '-_', '+/') . str_repeat('=', $iPadding),
            true
        );

        if (!is_string($sDecodedReference)) {
            return null;
        }

        $aReferenceParts = explode('|', $sDecodedReference, 2);
        if (count($aReferenceParts) !== 2 || preg_match('/^[1-9][0-9]*$/', $aReferenceParts[0]) !== 1) {
            return null;
        }

        $mMembershipId = filter_var(
            $aReferenceParts[0],
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        $sToken = $aReferenceParts[1];
        if ($mMembershipId === false || !self::isValidToken($sToken)) {
            return null;
        }

        return [
            'membership_id' => $mMembershipId,
            'token' => $sToken
        ];
    }

    public static function isPurchasableMembership(mixed $mMembership): bool
    {
        return $mMembership instanceof \stdClass
            && (int)($mMembership->enable ?? 0) === 1
            && self::toMinorUnits($mMembership->price ?? '') > 0;
    }

    /**
     * Return the configured membership price including VAT, rounded to the
     * currency's two-decimal minor unit.
     */
    public static function getTotalAmount(string|int|float $mPrice, string|int|float $mVatRate): string
    {
        $iPrice = self::toMinorUnits($mPrice);
        $iVatRate = self::toVatUnits($mVatRate);
        if ($iPrice === null || $iVatRate === null) {
            throw new \InvalidArgumentException('The payment amount or VAT rate is invalid.');
        }

        $iTax = intdiv(($iPrice * $iVatRate) + intdiv(self::PERCENT_SCALE, 2), self::PERCENT_SCALE);

        return self::formatMinorUnits($iPrice + $iTax);
    }

    public static function getMinorUnits(string|int|float $mAmount): int
    {
        $iAmount = self::toMinorUnits($mAmount);
        if ($iAmount === null) {
            throw new \InvalidArgumentException('The payment amount is invalid.');
        }

        return $iAmount;
    }

    public static function isExpectedAmount(
        string|int|float $mPrice,
        string|int|float $mVatRate,
        string|int|float $mPaidAmount
    ): bool {
        $iPaidAmount = self::toMinorUnits($mPaidAmount);

        return $iPaidAmount !== null
            && self::getTotalAmount($mPrice, $mVatRate) === self::formatMinorUnits($iPaidAmount);
    }

    public static function isValidPayPalNotification(
        array $aPayment,
        string $sCheckoutReferenceHash,
        int $iMembershipId,
        string $sMerchantEmail,
        string $sCurrency,
        string|int|float $mExpectedAmount
    ): bool {
        $aRequiredFields = [
            'custom',
            'payment_status',
            'txn_id',
            'receiver_email',
            'mc_currency',
            'mc_gross',
            'item_number'
        ];
        foreach ($aRequiredFields as $sFieldName) {
            if (
                !isset($aPayment[$sFieldName])
                || !is_scalar($aPayment[$sFieldName])
                || is_bool($aPayment[$sFieldName])
            ) {
                return false;
            }
        }

        $sTransactionId = trim((string)$aPayment['txn_id']);
        $sPostedMembershipId = (string)$aPayment['item_number'];

        $sCheckoutReference = (string)$aPayment['custom'];

        return self::isPayPalReference($sCheckoutReference)
            && preg_match('/^[a-f0-9]{64}$/', $sCheckoutReferenceHash) === 1
            && hash_equals($sCheckoutReferenceHash, hash('sha256', $sCheckoutReference))
            && $aPayment['payment_status'] === 'Completed'
            && $sTransactionId !== ''
            && strlen($sTransactionId) <= 127
            && preg_match('/^[1-9][0-9]*$/', $sPostedMembershipId) === 1
            && (int)$sPostedMembershipId === $iMembershipId
            && strcasecmp(trim((string)$aPayment['receiver_email']), trim($sMerchantEmail)) === 0
            && strtoupper((string)$aPayment['mc_currency']) === strtoupper($sCurrency)
            && self::isExpectedAmount($mExpectedAmount, 0, $aPayment['mc_gross']);
    }

    private static function isValidToken(string $sToken): bool
    {
        return preg_match('/^[a-f0-9]{40}$/', $sToken) === 1;
    }

    private static function toMinorUnits(string|int|float $mAmount): ?int
    {
        $sAmount = trim((string)$mAmount);
        if (preg_match('/^(\d{1,10})(?:\.(\d{1,2}))?$/', $sAmount, $aMatches) !== 1) {
            return null;
        }

        $sDecimals = str_pad($aMatches[2] ?? '', 2, '0');

        return ((int)$aMatches[1] * self::PRICE_SCALE) + (int)$sDecimals;
    }

    private static function toVatUnits(string|int|float $mVatRate): ?int
    {
        $sVatRate = trim((string)$mVatRate);
        if (preg_match('/^(\d{1,3})(?:\.(\d{1,3}))?$/', $sVatRate, $aMatches) !== 1) {
            return null;
        }

        $iVatRate = ((int)$aMatches[1] * self::VAT_SCALE) +
            (int)str_pad($aMatches[2] ?? '', 3, '0');

        return $iVatRate <= self::PERCENT_SCALE ? $iVatRate : null;
    }

    private static function formatMinorUnits(int $iAmount): string
    {
        return sprintf('%d.%02d', intdiv($iAmount, self::PRICE_SCALE), $iAmount % self::PRICE_SCALE);
    }
}
