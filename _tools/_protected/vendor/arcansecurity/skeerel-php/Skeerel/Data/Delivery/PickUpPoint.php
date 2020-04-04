<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Delivery;


use Skeerel\Data\Address\Country;
use Skeerel\Exception\IllegalArgumentException;

class PickUpPoint implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $primary;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $zipCode;

    /**
     * @var string
     */
    private $city;

    /**
     * @var Country
     */
    private $country;

    /**
     * @var string
     */
    private $deliveryTextContent;

    /**
     * @var Color
     */
    private $deliveryTextColor;

    /**
     * @var int
     */
    private $price;

    /**
     * @var Color
     */
    private $priceTextColor;

    /**
     * PickUpPoint constructor.
     */
    public function __construct(){
        $this->primary = false;
    }

    /**
     * @param string $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @param bool $primary
     */
    public function setPrimary($primary) {
        $this->primary = $primary;
    }

    /**
     * @param string $address
     */
    public function setAddress($address) {
        $this->address = $address;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode($zipCode) {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $city
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country) {
        $this->country = $country;
    }

    /**
     * @param string $deliveryTextContent
     */
    public function setDeliveryTextContent($deliveryTextContent) {
        $this->deliveryTextContent = $deliveryTextContent;
    }

    /**
     * @param Color $deliveryTextColor
     */
    public function setDeliveryTextColor(Color $deliveryTextColor) {
        $this->deliveryTextColor = $deliveryTextColor;
    }

    /**
     * @param int $price
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * @param Color $priceTextColor
     */
    public function setPriceTextColor(Color $priceTextColor) {
        $this->priceTextColor = $priceTextColor;
    }


    /**
     * @return array
     * @throws IllegalArgumentException
     */
    public function jsonSerialize() {
        if ($this->id === null || $this->primary === null || $this->name === null || $this->address === null
            || $this->zipCode === null || $this->city === null || $this->country === null) {
            throw new IllegalArgumentException("Not all mandatory fields are set");
        }

        $result = array(
            "id" => $this->id,
            "name" => $this->name,
            "primary" => $this->primary,
            "address" => $this->address,
            "zip_code" => $this->zipCode,
            "city" => $this->city,
            "country" => $this->country->getAlpha2(),
        );

        if ($this->deliveryTextContent != null) {
            $result['delivery_text_content'] = $this->deliveryTextContent;
            if ($this->deliveryTextColor != null) {
                $result['delivery_text_color'] = $this->deliveryTextColor->getValue();
            }
        }

        if ($this->price != null && $this->price > 0) {
            $result['price'] = $this->price;
            if ($this->priceTextColor != null) {
                $result['price_text_color'] = $this->priceTextColor->getValue();
            }
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
            $tab . "name => $this->name,\n" .
            $tab . "primary => $this->primary,\n" .
            $tab . "address => $this->address,\n" .
            $tab . "zipCode => $this->zipCode,\n" .
            $tab . "city => $this->city,\n" .
            $tab . "country => $this->country,\n" .
            $tab . "deliveryTextContent => $this->deliveryTextContent,\n" .
            $tab . "deliveryTextColor => $this->deliveryTextColor,\n" .
            $tab . "price => $this->price,\n" .
            $tab . "priceTextColor => $this->priceTextColor,\n" .
            $tab2 . "}";
    }
}