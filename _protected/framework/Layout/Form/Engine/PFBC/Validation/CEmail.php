<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use \PH7\ExistsCoreModel, PH7\Framework\Security\Ban\Ban;

class CEmail extends \PFBC\Validation {
    protected $sTable, $sType;

    public function __construct($sType = '', $sTable = 'Members') {
        parent::__construct();
        $this->sTable = $sTable;
        $this->sType = $sType;
        $this->message = t('Error: Invalid email address or this email is already used by another member.');
    }

    public function isValid($sValue) {
        $sEmailHost = strrchr($sValue, '@');

        if($this->isNotApplicable($sValue) || (!Ban::isEmail($sValue) && !Ban::isEmail($sEmailHost) && $this->oValidate->email($sValue))) {
            if($this->sType == 'guest' && (new ExistsCoreModel)->email($sValue, $this->sTable)) {
                return false;
            }
            return true;
        }
        return false;
    }
}
