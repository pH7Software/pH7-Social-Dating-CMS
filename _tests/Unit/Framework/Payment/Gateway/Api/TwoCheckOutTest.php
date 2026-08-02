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
    private array $aGetBackup = [];
    private array $aRequestBackup = [];

    protected function setUp(): void
    {
        $this->aPostBackup = $_POST;
        $this->aGetBackup = $_GET;
        $this->aRequestBackup = $_REQUEST;
    }

    protected function tearDown(): void
    {
        $_POST = $this->aPostBackup;
        $_GET = $this->aGetBackup;
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
            'fraud_status' => 'pass',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'md5_hash' => strtoupper(md5($sPayload)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testFraudStatusChangedAcceptsSha256HmacWithPrefix(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sSecretKey = 'secret_key';
        $sSaleId = '1001';
        $sInvoiceId = '9001';
        $sPayload = $sSaleId . $sVendorId . $sInvoiceId . $sSecretWord;

        $_POST = [
            'message_type' => 'FRAUD_STATUS_CHANGED',
            'fraud_status' => 'pass',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'hash' => 'SHA256:' . strtoupper(hash_hmac('sha256', $sPayload, $sSecretKey)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord, $sSecretKey));
    }

    public function testFraudStatusChangedRejectsPendingFraudReview(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sSaleId = '1001';
        $sInvoiceId = '9001';
        $sPayload = $sSaleId . $sVendorId . $sInvoiceId . $sSecretWord;

        $_POST = [
            'message_type' => 'FRAUD_STATUS_CHANGED',
            'fraud_status' => 'wait',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'md5_hash' => strtoupper(md5($sPayload)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertFalse($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testFraudStatusChangedRejectsUnkeyedShaSignature(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sSaleId = '1001';
        $sInvoiceId = '9001';
        $sPayload = $sSaleId . $sVendorId . $sInvoiceId . $sSecretWord;

        $_POST = [
            'message_type' => 'FRAUD_STATUS_CHANGED',
            'fraud_status' => 'pass',
            'sale_id' => $sSaleId,
            'invoice_id' => $sInvoiceId,
            'hash' => 'SHA256:' . strtoupper(hash('sha256', $sPayload)),
        ];
        $_REQUEST = $_POST;

        $oGateway = new TwoCheckOut(false);

        $this->assertFalse($oGateway->valid($sVendorId, $sSecretWord, 'secret_key'));
    }

    public function testPurchaseReturnAcceptsLegacyMd5Key(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sOrderNumber = '7001';
        $sTotal = '39.99';
        $sPayload = $sSecretWord . $sVendorId . $sOrderNumber . $sTotal;

        $_GET = [
            'order_number' => $sOrderNumber,
            'total' => $sTotal,
            'key' => strtoupper(md5($sPayload)),
        ];
        $_POST = [];
        $_REQUEST = $_GET;

        $oGateway = new TwoCheckOut(false);

        $this->assertTrue($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testPurchaseReturnRejectsUndocumentedSha256Key(): void
    {
        $sVendorId = 'vendor123';
        $sSecretWord = 'secret_word';
        $sOrderNumber = '7001';
        $sTotal = '39.99';
        $sPayload = $sSecretWord . $sVendorId . $sOrderNumber . $sTotal;

        $_GET = [
            'order_number' => $sOrderNumber,
            'total' => $sTotal,
            'key' => strtoupper(hash('sha256', $sPayload)),
        ];
        $_POST = [];
        $_REQUEST = $_GET;

        $oGateway = new TwoCheckOut(false);

        $this->assertFalse($oGateway->valid($sVendorId, $sSecretWord));
    }

    public function testPurchaseReturnRejectsNonScalarFields(): void
    {
        $_GET = [
            'order_number' => ['7001'],
            'total' => '39.99',
            'key' => 'INVALID',
        ];
        $_POST = [];
        $_REQUEST = $_GET;

        $oGateway = new TwoCheckOut(false);

        $this->assertFalse($oGateway->valid('vendor123', 'secret_word'));
    }
}
