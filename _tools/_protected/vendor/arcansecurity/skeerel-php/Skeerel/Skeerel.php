<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel;

use Skeerel\Data\Data;
use Skeerel\Data\Payment\Payment;
use Skeerel\Data\Website\Website;
use Skeerel\Exception\APIException;
use Skeerel\Exception\IllegalArgumentException;
use Skeerel\Util\Random;
use Skeerel\Util\Request;
use Skeerel\Util\Session;
use Skeerel\Util\UUID;

class Skeerel
{
    /**
     * @var string
     */
    const API_BASE = 'https://api.skeerel.com/v2/';

    /**
     * @var string
     */
    const DEFAULT_COOKIE_NAME = 'skeerel-state';

    /**
     * @var string
     */
    private $websiteID;

    /**
     * @var string
     */
    private $websiteSecret;

    /**
     * Skeerel constructor.
     * @param $websiteID
     * @param $websiteSecret
     * @throws IllegalArgumentException
     */
    public function __construct($websiteID, $websiteSecret) {
        if (!is_string($websiteID) || !UUID::isValid($websiteID)) {
            throw new IllegalArgumentException("websiteId must be a string UUID");
        }

        if (!is_string($websiteSecret)) {
            throw new IllegalArgumentException("websiteSecret must be a string");
        }

        $this->websiteID = $websiteID;
        $this->websiteSecret = $websiteSecret;
    }

    /**
     * @param $token
     * @return Data
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function getData($token) {
        if (!is_string($token)) {
            throw new IllegalArgumentException("token must be a string");
        }

        $json = Request::getJson(self::API_BASE . 'me', array(
            "access_token" => $token,
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        ));

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        if (!isset($json['data'])) {
            throw new APIException("Unexpected error: status is ok, but cannot get data");
        }

        return new Data($json['data']);
    }

    /**
     * @param $paymentId
     * @return Payment
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function getPayment($paymentId) {
        if (!is_string($paymentId)) {
            throw new IllegalArgumentException("paymentId must be a string");
        }

        $json = Request::getJson(self::API_BASE . 'payment/get', array(
            "payment_id" => $paymentId,
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        ));

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        if (!isset($json['data'])) {
            throw new APIException("Unexpected error: status is ok, but cannot get data");
        }

        return new Payment($json['data']);
    }

    /**
     * @param bool|null $live
     * @param int|null $first
     * @param int|null $limit
     * @return Payment[]
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function listPayments($live = null, $first = null, $limit = null) {
        $parameters = array(
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        );

        if (is_bool($live)) {
            $parameters["live"] = $live;
        }

        if (is_int($first)) {
            $parameters["first"] = $first;
        }

        if (is_int($limit)) {
            $parameters["limit"] = $limit;
        }

        $json = Request::getJson(self::API_BASE . 'payment/list', $parameters);

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        if (!isset($json['data'])) {
            throw new APIException("Unexpected error: status is ok, but cannot get data");
        }

        $payments = array();
        foreach ($json['data'] as $payment) {
            array_push($payments, new Payment($payment));
        }

        return $payments;
    }

    /**
     * @param string $paymentId
     * @param int $amount
     * @return bool
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function refundPayment($paymentId, $amount = null) {
        if (!is_string($paymentId)) {
            throw new IllegalArgumentException("paymentId must be a string");
        }

        $parameters = array(
            "payment_id" => $paymentId,
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        );

        if ($amount != null) {
            if (!is_int($amount) || $amount <= 0) {
                throw new IllegalArgumentException("amount to be refunded must be an integer. Got " . gettype($amount));
            }
            $parameters["amount"] = $amount;
        }

        $json = Request::getJson(self::API_BASE . 'payment/refund', $parameters);

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        return true;
    }

    /**
     * @param string $paymentId
     * @return bool
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function capturePayment($paymentId) {
        if (!is_string($paymentId)) {
            throw new IllegalArgumentException("paymentId must be a string");
        }

        $parameters = array(
            "payment_id" => $paymentId,
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        );

        $json = Request::getJson(self::API_BASE . 'payment/capture', $parameters);

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        return true;
    }

    /**
     * @param string $paymentId
     * @return bool
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function rejectPayment($paymentId) {
        if (!is_string($paymentId)) {
            throw new IllegalArgumentException("paymentId must be a string");
        }

        $parameters = array(
            "payment_id" => $paymentId,
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        );

        $json = Request::getJson(self::API_BASE . 'payment/reject', $parameters);

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        return true;
    }

    /**
     * @return Website
     * @throws APIException
     * @throws Exception\DecodingException
     * @throws IllegalArgumentException
     */
    public function getWebsiteDetails() {
        $json = Request::getJson(self::API_BASE . 'website/details', array(
            "website_id" => $this->websiteID,
            "website_secret" => $this->websiteSecret
        ));

        if (!isset($json['status']) || "ok" !== $json['status']) {
            $errorCode = isset($json['error_code']) && is_int($json['error_code']) ? $json['error_code'] : '';
            $errorMsg = isset($json['message']) && is_string($json['message']) ? $json['message'] : '';
            throw new APIException("Error " . $errorCode . ": " . $errorMsg);
        }

        if (!isset($json['data'])) {
            throw new APIException("Unexpected error: status is ok, but cannot get data");
        }

        return new Website($json['data']);
    }

    /**
     * @param string $sessionName
     * @throws Exception\SessionNotStartedException
     * @throws IllegalArgumentException
     */
    public static function generateSessionStateParameter($sessionName = self::DEFAULT_COOKIE_NAME) {
        Session::set($sessionName, Random::token());
    }

    /**
     * @param string $sessionName
     * @return bool
     * @throws Exception\SessionNotStartedException
     * @throws IllegalArgumentException
     */
    public static function isSessionStateParameterGenerated($sessionName = self::DEFAULT_COOKIE_NAME) {
        return Session::get($sessionName) !== null;
    }

    /**
     * @param $stateValue
     * @param string $sessionName
     * @return bool
     * @throws Exception\SessionNotStartedException
     * @throws IllegalArgumentException
     */
    public static function verifySessionStateParameter($stateValue, $sessionName = self::DEFAULT_COOKIE_NAME) {
        return Session::get($sessionName) === $stateValue;
    }

    /**
     * @param $stateValue
     * @param string $sessionName
     * @return bool
     * @throws Exception\SessionNotStartedException
     * @throws IllegalArgumentException
     */
    public static function verifyAndRemoveSessionStateParameter($stateValue, $sessionName = self::DEFAULT_COOKIE_NAME) {
        $result = Session::get($sessionName) === $stateValue;
        Session::remove($sessionName);
        return $result;
    }
}