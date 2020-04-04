<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


use Skeerel\Exception\IllegalArgumentException;

class DeliveryMethod implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var bool
     */
    private $primary;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $deliveryTextContent;

    /**
     * @var int
     */
    private $price;

    /**
     * @var PickUpPoints
     */
    private $pickUpPoints;


    /**
     * DeliveryMethod constructor.
     */
    function __construct() {
        $this->primary = false;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param Type $type
     */
    public function setType(Type $type) {
        $this->type = $type;
    }

    /**
     * @param bool $primary
     */
    public function setPrimary($primary) {
        $this->primary = $primary;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param string $deliveryTextContent
     */
    public function setDeliveryTextContent($deliveryTextContent) {
        $this->deliveryTextContent = $deliveryTextContent;
    }

    /**
     * @param int $price
     * @throws IllegalArgumentException
     */
    public function setPrice($price) {
        if ($price < 0) {
            throw new IllegalArgumentException("Cannot set a price lower than 0");
        }
        $this->price = $price;
    }

    /**
     * @param PickUpPoints $pickUpPoints
     */
    public function setPickUpPoints(PickUpPoints $pickUpPoints) {
        $this->pickUpPoints = $pickUpPoints;
    }



    /**
     * @return array
     * @throws IllegalArgumentException
     */
    public function jsonSerialize() {
        if ($this->id === null || $this->type === null || $this->primary === null || $this->name === null
            || $this->deliveryTextContent === null || $this->price === null) {
            throw new IllegalArgumentException("Not all mandatory fields are set");
        }

        if ($this->type->equals(Type::HOME()) && $this->pickUpPoints !== null) {
            throw new IllegalArgumentException("Home delivery cannot have pick up points");
        }

        if (!$this->type->equals(Type::HOME()) && ($this->pickUpPoints === null || $this->pickUpPoints->isEmpty())) {
            throw new IllegalArgumentException("$this->type must have pick up points");
        }

        $result = array(
            "id" => $this->id,
            "type" => $this->type->getValue(),
            "primary" => $this->primary,
            "name" => $this->name,
            "delivery_text_content" => $this->deliveryTextContent,
            "price" => $this->price
        );

        if ($this->pickUpPoints != null) {
            $result["pick_up_points"] = $this->pickUpPoints->jsonSerialize();
        }

        return $result;
    }

    /**
     * @return string
     * @throws IllegalArgumentException
     */
    public function toJson() {
        return json_encode($this->jsonSerialize());
    }
}