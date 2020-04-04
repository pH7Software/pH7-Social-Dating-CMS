<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Address;


use Skeerel\Exception\IllegalArgumentException;

class CompanyAddress extends BaseAddress
{
    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $companyName;

    /**
     * @var string
     */
    private $vatNumber;

    /**
     * CompanyAddress constructor.
     * @param array $address
     * @throws IllegalArgumentException
     */
    public function __construct($address) {
        parent::__construct($address);

        $this->setStatus($address);
        $this->setCompanyName($address);
        $this->setVatNumber($address);
    }

    /**
     * @param array $address
     */
    private function setStatus($address) {
        if (isset($address['status']) && is_string($address['status'])) {
            $this->status = $address['status'];
        }
    }

    /**
     * @param array $address
     */
    private function setCompanyName($address) {
        if (isset($address['company_name']) && is_string($address['company_name'])) {
            $this->companyName = $address['company_name'];
        }
    }

    /**
     * @param array $address
     */
    private function setVatNumber($address) {
        if (isset($address['vat']) && is_string($address['vat'])) {
            $this->vatNumber = $address['vat'];
        }
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getCompanyName() {
        return $this->companyName;
    }

    /**
     * @return string
     */
    public function getVatNumber() {
        return $this->vatNumber;
    }

    /**
     * @return bool
     */
    public function isIndividual() {
        return false;
    }

    /**
     * @return bool
     */
    public function isCompany() {
        return true;
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
                $tab . "status => $this->status,\n" .
                $tab . "companyName => $this->companyName,\n" .
                $tab . "vatNumber => $this->vatNumber,\n" .
                parent::toString($level) . "\n" .
            $tab2 . "}";
    }
}