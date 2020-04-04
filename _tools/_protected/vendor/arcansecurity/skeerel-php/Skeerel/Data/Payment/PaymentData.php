<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Payment;


use Skeerel\Data\Address\BaseAddress;
use Skeerel\Exception\IllegalArgumentException;

class PaymentData
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var bool
     */
    private $live;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var bool
     */
    private $captured;

    /**
     * @var BaseAddress
     */
    private $billingAddress;

    /**
     * @var int
     */
    private $paymentErrorCode;

    /**
     * @var string
     */
    private $paymentErrorMessage;

    /**
     * Payment constructor.
     * @param array $data
     * @throws IllegalArgumentException
     */
    public function __construct($data) {
        if (!is_array($data)) {
            throw new IllegalArgumentException("Payment cannot be parsed due to incorrect data");
        }

        if (isset($data['id']) && is_string($data['id'])) {
            $this->id = $data['id'];
        }

        if (isset($data['live']) && is_bool($data['live'])) {
            $this->live = $data['live'];
        }

        if (isset($data['amount']) && is_int($data['amount'])) {
            $this->amount = $data['amount'];
        }

        if (isset($data['currency']) && is_string($data['currency'])) {
            $this->currency = Currency::fromStrValue($data['currency'], true);
        }

        if (isset($data['captured']) && is_bool($data['captured'])) {
            $this->captured = $data['captured'];
        }

        if (isset($data['billing_address'])) {
            $this->billingAddress = BaseAddress::build($data['billing_address']);
        }

        if (isset($data['payment_error_code']) && is_int($data['payment_error_code'])) {
            $this->paymentErrorCode = $data['payment_error_code'];
        }

        if (isset($data['payment_error_message']) && is_string($data['payment_error_message'])) {
            $this->paymentErrorMessage = $data['payment_error_message'];
        }
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isLive() {
        return $this->live;
    }

    /**
     * @return int
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency() {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isCaptured() {
        return $this->captured;
    }

    /**
     * @return BaseAddress
     */
    public function getBillingAddress() {
        return $this->billingAddress;
    }

    /**
     * @return int
     */
    public function getPaymentErrorCode() {
        return $this->paymentErrorCode;
    }

    /**
     * @return string
     */
    public function getPaymentErrorMessage() {
        return $this->paymentErrorMessage;
    }

    /**
     * @return bool
     */
    public function isError() {
        return $this->paymentErrorCode != null || $this->paymentErrorMessage != null;
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->toString();
    }

    /**
     * @param int $level
     * @return string
     */
    public function toString($level = 1) {
        $tab = str_repeat("\t", $level);
        $tab2 = str_repeat("\t", $level-1);

        return
            "{\n" .
            $tab . "id => $this->id,\n" .
            $tab . "live => $this->live,\n" .
            $tab . "amount => $this->amount,\n" .
            $tab . "currency => $this->currency,\n" .
            $tab . "captured => $this->captured,\n" .
            $tab . "billingAddress => " . $this->billingAddress->toString($level + 1) . ",\n" .
            $tab . "paymentErrorCode => $this->paymentErrorCode,\n" .
            $tab . "paymentErrorMessage => $this->paymentErrorMessage,\n" .
            $tab2 . "}";
    }
}
