<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2026, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Test / Unit / Framework / Payment / Gateway / Api
 */

declare(strict_types=1);

namespace PH7\Test\Unit\Framework\Payment\Gateway\Api;

use PH7\Framework\Payment\Gateway\Api\TwoCheckOut;
use PHPUnit\Framework\TestCase;

final class TwoCheckOutTest extends TestCase
{
    private array $aPostBackup = [];
    private array $aRequestBackup = [];

    protected function setUp(): void
    {
        $this->aPostBackup = $_POST;
        $this->aRequestBackup = $_REQUEST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->aPostBackup;
        $_REQUEST = $this->aRequestBackup;
    }

    public function testFraudStatusChangedAcceptsLegacyMd5Hash(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sSaleId = '1001';
        $sInvoiceId = '9001';
        $sPayload = $sSaleId . $sVendorId . $sInvoiceId . $sSecretWord;

        $_POST = [
            'message_type' => 'FRAUD_STATUS_CHANGED',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'md5_hash' => strtoupper(md5($sPayload)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testFraudStatusChangedAcceptsSha256HashWithPrefix(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sSaleId = '1001';
        $sInvoiceId = '9001';
        $sPayload = $sSaleId . $sVendorId . $sInvoiceId . $sSecretWord;

        $_POST = [
            'message_type' => 'FRAUD_STATUS_CHANGED',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'hash' => 'SHA256:' . strtoupper(hash('sha256', $sPayload)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testPurchaseReturnAcceptsLegacyMd5Key(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sOrderNumber = '7001';
        $sTotal = '39.99';
        $sPayload = $sSecretWord . $sVendorId . $sOrderNumber . $sTotal;

        $_POST = [
            'order_number' => $sOrderNumber,
            'total' => $sTotal,
        ];
        $_REQUEST = $_POST + ['key' => strtoupper(md5($sPayload))];

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testPurchaseReturnAcceptsSha256Key(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sOrderNumber = '7001';
        $sTotal = '39.99';
        $sPayload = $sSecretWord . $sVendorId . $sOrderNumber . $sTotal;

        $_POST = [
            'order_number' => $sOrderNumber,
            'total' => $sTotal,
        ];
        $_REQUEST = $_POST + ['key' => strtoupper(hash('sha256', $sPayload))];

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }
}
