<?php
/**
 * Created by Florian Pradines
 */

namespace Skeerel\Data\Address;


use Skeerel\Exception\IllegalArgumentException;

class IndividualAddress extends BaseAddress
{
    /**
     * IndividualAddress constructor.
     * @param $address
     * @throws IllegalArgumentException
     */
    public function __construct($address) {
        parent::__construct($address);
    }

    /**
     * @return bool
     */
    public function isIndividual() {
        return true;
    }

    /**
     * @return bool
     */
    public function isCompany() {
        return false;
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
        $tab2 = str_repeat("\t", $level-1);

        return
            "{\n" .
                parent::toString($level) . "\n" .
            $tab2 . "}";
    }
}