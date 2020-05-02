<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\DbTableName;
use PH7\ExistsCoreModel;

class CEmail extends Validation
{
    const GUEST_MODE = 'guest';

    /** @var string */
    protected $sTable;

    /** @var string */
    protected $sType;

    public function __construct($sType = '', $sTable = DbTableName::MEMBER)
    {
        parent::__construct();

        $this->sTable = $sTable;
        $this->sType = $sType;
        $this->message = t('Error: Invalid email or already used by another user.');
    }

    public function isValid($sValue)
    {
        if ($this->isNotApplicable($sValue) || $this->oValidate->email($sValue)) {
            return !($this->sType === self::GUEST_MODE && (new ExistsCoreModel)->email($sValue, $this->sTable));
        }

        return false;
    }
}
