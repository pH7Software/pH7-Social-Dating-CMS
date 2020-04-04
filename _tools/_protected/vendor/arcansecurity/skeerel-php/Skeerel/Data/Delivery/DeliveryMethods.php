<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


class DeliveryMethods implements \JsonSerializable
{
    /**
     * @var DeliveryMethod[]
     */
    private $deliveryMethods;

    public function __construct() {
        $this->deliveryMethods = array();
    }

    /**
     * @param DeliveryMethod $deliveryMethod
     */
    public function add(DeliveryMethod $deliveryMethod) {
        array_push($this->deliveryMethods, $deliveryMethod);
    }

    /**
     * @return int
     */
    public function size() {
        return sizeof($this->deliveryMethods);
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return $this->size() === 0;
    }

    /**
     * @return DeliveryMethod[]
     */
    public function jsonSerialize() {
        return $this->deliveryMethods;
    }

    /**
     * @return string
     */
    public function toJson() {
        return json_encode($this->jsonSerialize());
    }
}