<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data;


use Skeerel\Data\Delivery\Delivery;
use Skeerel\Data\Payment\PaymentData;
use Skeerel\Exception\IllegalArgumentException;

class Data
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $custom;

    /**
     * @var Delivery
     */
    private $delivery;

    /**
     * @var PaymentData
     */
    private $payment;

    /**
     * User constructor.
     * @param array $data
     * @throws IllegalArgumentException
     */
    function __construct($data) {
        if (!is_array($data) || !isset($data['user']) || !is_array($data['user'])) {
            throw new IllegalArgumentException("Data cannot be parsed due to incorrect data");
        }

        $this->user = new User($data['user']);

        if (isset($data['custom']) && is_string($data['custom'])) {
            $this->custom = $data['custom'];
        }

        if (isset($data['payment'])) {
            $this->payment = new PaymentData($data['payment']);

            if (isset($data['delivery'])) {
                $this->delivery = new Delivery($data['delivery']);
            }
        }
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @return Delivery
     */
    public function getDelivery() {
        return $this->delivery;
    }

    /**
     * @return PaymentData
     */
    public function getPayment() {
        return $this->payment;
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
                $tab . "user => " . $this->user->toString($level+1) . ",\n" .
                $tab . "custom => " . $this->custom . ",\n" .
                $tab . "delivery => " . ($this->delivery !== null ? $this->delivery->toString($level+1) : "") .",\n" .
                $tab . "payment => " . ($this->payment !== null ? $this->payment->toString($level+1) : "") . ",\n" .
            $tab2 . "}";
    }
}