<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\ExistsCoreModel;

class CEmail extends \PFBC\Validation
{
    protected $sTable, $sType;

    public function __construct($sType = '', $sTable = 'Members')
    {
        parent::__construct();
        $this->sTable = $sTable;
        $this->sType = $sType;
        $this->message = t('Error: Invalid email address or this email is already used by another member.');
    }

    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue) || $this->oValidate->email($sValue))
            return !($this->sType == 'guest' && (new ExistsCoreModel)->email($sValue, $this->sTable));

        return false;
    }
}
