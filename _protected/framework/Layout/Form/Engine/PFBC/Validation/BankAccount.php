<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\DbTableName;
use PH7\ExistsCoreModel;
use PH7\Framework\Security\Ban\Ban;

class BankAccount extends Validation
{
    /** @var string */
    protected $sTable;

    /**
     * @param string $sTable
     */
    public function __construct($sTable = DbTableName::AFFILIATE)
    {
        parent::__construct();
        $this->sTable = $sTable;
    }

    /**
     * @param string $sValue
     *
     * @return bool
     */
    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue) || $this->oValidate->email($sValue)) {
            if (!Ban::isBankAccount($sValue)) {
                if (!(new ExistsCoreModel)->bankAccount($sValue, $this->sTable)) {
                    return true;
                } else {
                    $this->message = t('Error: Another account with the same bank account already exists. Please choose another one.');
                }
            } else {
                $this->message = t('Error: This bank account is not supported by our payment system.');
            }
        } else {
            $this->message = t('Error: Your bank account is incorrect.');
        }

        return false;
    }
}
