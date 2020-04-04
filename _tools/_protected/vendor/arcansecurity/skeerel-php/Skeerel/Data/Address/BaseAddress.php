<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Address;


use Skeerel\Exception\IllegalArgumentException;

abstract class BaseAddress
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $addressLine2;

    /**
     * @var string
     */
    private $addressLine3;

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
     * @var
     */
    private $phone;

    /**
     * BaseAddress constructor.
     * @param array $address
     * @throws IllegalArgumentException
     */
    protected function __construct($address) {
        if (!is_array($address)) {
            throw new IllegalArgumentException("address should be an array");
        }

        $this->setName($address);
        $this->setAddress($address);
        $this->setAddressLine2($address);
        $this->setAddressLine3($address);
        $this->setZipCode($address);
        $this->setCity($address);
        $this->setCountry($address);
        $this->setPhone($address);
    }

    /**
     * @param array $address
     */
    private function setName($address) {
        if (isset($address['name']) && is_string($address['name'])) {
            $this->name = $address['name'];
        }
    }

    /**
     * @param array $address
     */
    private function setAddress($address) {
        if (isset($address['address']) && is_string($address['address'])) {
            $this->address = $address['address'];
        }
    }

    /**
     * @param array $address
     */
    private function setAddressLine2($address) {
        if (isset($address['address_line_2']) && is_string($address['address_line_2'])) {
            $this->addressLine2 = $address['address_line_2'];
        }
    }

    /**
     * @param array $address
     */
    private function setAddressLine3($address) {
        if (isset($address['address_line_3']) && is_string($address['address_line_3'])) {
            $this->addressLine3 = $address['address_line_3'];
        }
    }

    /**
     * @param array $address
     */
    private function setZipCode($address) {
        if (isset($address['zip_code']) && is_string($address['zip_code'])) {
            $this->zipCode = $address['zip_code'];
        }
    }

    /**
     * @param array $address
     */
    private function setCity($address) {
        if (isset($address['city']) && is_string($address['city'])) {
            $this->city = $address['city'];
        }
    }

    /**
     * @param array $address
     */
    private function setCountry($address){
        if (isset($address['country_code'])) {
            $this->country = Country::fromAlpha2($address['country_code']);
        }
    }

    /**
     * @param array $address
     */
    private function setPhone($address) {
        if (isset($address['phone_number']) && is_string($address['phone_number']) &&
            preg_match('/^\+[1-9]\d{9,14}$/', str_replace(' ', '', $address['phone_number'])) === 1) {
            $this->phone = str_replace(' ', '', $address['phone_number']);
        }
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getAddressLine2() {
        return $this->addressLine2;
    }

    /**
     * @return string
     */
    public function getAddressLine3() {
        return $this->addressLine3;
    }

    /**
     * @return string
     */
    public function getZipCode() {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     * @return Country
     */
    public function getCountry() {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getPhone() {
        return $this->phone;
    }



    /**
     * @return bool
     */
    public abstract function isIndividual();

    /**
     * @return bool
     */
    public abstract function isCompany();

    /**
     * @return string
     */
    public function __toString() {
        return
            "\t name => $this->name,\n" .
            "\t address => $this->address,\n" .
            "\t addressLine2 => $this->addressLine2,\n" .
            "\t addressLine3 => $this->addressLine3,\n" .
            "\t zipCode => $this->zipCode,\n" .
            "\t city => $this->city,\n" .
            "\t country => $this->country,\n" .
            "\t phone => $this->phone";
    }

    /**
     * @param int $level
     * @return string
     */
    public function toString($level = 1) {
        $tab = str_repeat("\t", $level);

        return
            $tab . "name => $this->name,\n" .
            $tab . "address => $this->address,\n" .
            $tab . "addressLine2 => $this->addressLine2,\n" .
            $tab . "addressLine3 => $this->addressLine3,\n" .
            $tab . "zipCode => $this->zipCode,\n" .
            $tab . "city => $this->city,\n" .
            $tab . "country => $this->country,\n" .
            $tab . "phone => $this->phone";
    }

    /**
     * @param $address
     * @return CompanyAddress|IndividualAddress
     * @throws IllegalArgumentException
     */
    public static function build($address) {
        if (!is_array($address)) {
            throw new IllegalArgumentException("address should be an array");
        }

        if (isset($address['company_name'])) {
            return new CompanyAddress($address);
        }

        return new IndividualAddress($address);
    }
}