<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Payment;


use DateTime;
use Skeerel\Exception\IllegalArgumentException;

class Payment
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @var string
     */
    private $profileId;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $live;

    /**
     * @var bool
     */
    private $captured;

    /**
     * @var DateTime
     */
    private $dateCaptured;

    /**
     * @var bool
     */
    private $refunded;

    /**
     * @var DateTime
     */
    private $dateRefunded;

    /**
     * @var bool
     */
    private $reviewed;

    /**
     * @var DateTime
     */
    private $dateReviewed;

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

        if (isset($data['date']) && is_string($data['date'])) {
            $this->date = new DateTime(str_replace("[UTC]", "", $data['date']));
        }

        if (isset($data['profile_id']) && is_string($data['profile_id'])) {
            $this->profileId = $data['profile_id'];
        }

        if (isset($data['amount']) && is_int($data['amount'])) {
            $this->amount = $data['amount'];
        }

        if (isset($data['currency']) && is_string($data['currency'])) {
            $this->currency = Currency::fromStrValue($data['currency'], true);
        }

        if (isset($data['status']) && is_string($data['status'])) {
            $this->status = Status::fromStrValue($data['status'], true);
        }

        if (isset($data['live']) && is_bool($data['live'])) {
            $this->live = $data['live'];
        }

        if (isset($data['captured']) && is_bool($data['captured'])) {
            $this->captured = $data['captured'];
        }

        if (isset($data['date_captured']) && is_string($data['date_captured'])) {
            $this->dateCaptured = new DateTime(str_replace("[UTC]", "", $data['date_captured']));
        }

        if (isset($data['refunded']) && is_bool($data['refunded'])) {
            $this->refunded = $data['refunded'];
        }

        if (isset($data['date_refunded']) && is_string($data['date_refunded'])) {
            $this->dateRefunded = new DateTime(str_replace("[UTC]", "", $data['date_refunded']));
        }

        if (isset($data['reviewed']) && is_bool($data['reviewed'])) {
            $this->reviewed = $data['reviewed'];
        }

        if (isset($data['date_reviewed']) && is_string($data['date_reviewed'])) {
            $this->dateReviewed = new DateTime(str_replace("[UTC]", "", $data['date_reviewed']));
        }
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @param string $profileId
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return bool
     */
    public function isLive()
    {
        return $this->live;
    }

    /**
     * @param bool $live
     */
    public function setLive($live)
    {
        $this->live = $live;
    }

    /**
     * @return bool
     */
    public function isCaptured()
    {
        return $this->captured;
    }

    /**
     * @param bool $captured
     */
    public function setCaptured($captured)
    {
        $this->captured = $captured;
    }

    /**
     * @return DateTime
     */
    public function getDateCaptured()
    {
        return $this->dateCaptured;
    }

    /**
     * @param DateTime $dateCaptured
     */
    public function setDateCaptured($dateCaptured)
    {
        $this->dateCaptured = $dateCaptured;
    }

    /**
     * @return bool
     */
    public function isRefunded()
    {
        return $this->refunded;
    }

    /**
     * @param bool $refunded
     */
    public function setRefunded($refunded)
    {
        $this->refunded = $refunded;
    }

    /**
     * @return DateTime
     */
    public function getDateRefunded()
    {
        return $this->dateRefunded;
    }

    /**
     * @param DateTime $dateRefunded
     */
    public function setDateRefunded($dateRefunded)
    {
        $this->dateRefunded = $dateRefunded;
    }

    /**
     * @return bool
     */
    public function isReviewed()
    {
        return $this->reviewed;
    }

    /**
     * @param bool $reviewed
     */
    public function setReviewed($reviewed)
    {
        $this->reviewed = $reviewed;
    }

    /**
     * @return DateTime
     */
    public function getDateReviewed()
    {
        return $this->dateReviewed;
    }

    /**
     * @param DateTime $dateReviewed
     */
    public function setDateReviewed($dateReviewed)
    {
        $this->dateReviewed = $dateReviewed;
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
                $tab . "date => " . $this->date->format(DateTime::ISO8601) . ",\n" .
                $tab . "profile_id => $this->profileId,\n" .
                $tab . "amount => $this->amount,\n" .
                $tab . "currency => $this->currency,\n" .
                $tab . "status => $this->status,\n" .
                $tab . "live => $this->live,\n" .
                $tab . "captured => $this->captured,\n" .
                $tab . "date_captured => " . ($this->dateCaptured !== null ? $this->dateCaptured->format(DateTime::ISO8601) : "") . ",\n" .
                $tab . "refunded => $this->refunded,\n" .
                $tab . "date_refunded => " . ($this->dateRefunded !== null ? $this->dateRefunded->format(DateTime::ISO8601) : "") . ",\n" .
                $tab . "reviewed => $this->reviewed,\n" .
                $tab . "date_reviewed => " . ($this->dateReviewed !== null ? $this->dateReviewed->format(DateTime::ISO8601) : "") . ",\n" .
            $tab2 . "}";
    }
}
