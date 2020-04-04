<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


class PickUpPoints implements \JsonSerializable
{
    /**
     * @var PickUpPoint[]
     */
    private $pickUpPoints;

    public function __construct() {
        $this->pickUpPoints = array();
    }

    /**
     * @param PickUpPoint $pickUpPoint
     */
    public function add(PickUpPoint $pickUpPoint) {
        array_push($this->pickUpPoints, $pickUpPoint);
    }

    /**
     * @return int
     */
    public function size() {
        return sizeof($this->pickUpPoints);
    }

    /**
     * @return bool
     */
    public function isEmpty() {
        return $this->size() === 0;
    }


    /**
     * @return PickUpPoint[]
     */
    public function jsonSerialize() {
        return $this->pickUpPoints;
    }

    /**
     * @return string
     */
    public function toJson() {
        return json_encode($this->jsonSerialize());
    }
}