<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Security\Ban\Ban, PH7\ExistsCoreModel;

class BankAccount extends \PFBC\Validation
{

    protected $sTable;

    /**
     * Constructor of class.
     *
     * @param $sTable Default 'Affiliate'
     */
    public function __construct($sTable = 'Affiliate')
    {
        parent::__construct();
        $this->sTable = $sTable;
    }

    /**
     * @param string $sValue
     * @return boolean
     */
    public function isValid($sValue)
    {
        $sEmailHost = strrchr($sValue, '@');

        if($this->isNotApplicable($sValue) || $this->oValidate->email($sValue)) {
            if(!Ban::isBankAccount($sValue) && !Ban::isBankAccount($sEmailHost)) {
                if(!(new ExistsCoreModel)->bankAccount($sValue, $this->sTable)) {
                    return true;
                } else {
                    $this->message = t('Error: Another account with the same bank account even exists. Please choose another.');
                }
            } else {
                $this->message = t('Sorry, This bank account is not supported by our payment system.');
            }
        } else {
            $this->message = t('Error: Your bank account is incorrect!');
        }
        return false;
    }

}
