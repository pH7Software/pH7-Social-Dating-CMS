<?php
/**
 * By Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Validation;

use PFBC\Validation;
use PH7\DbTableName;
use PH7\Framework\Mvc\Model\DbConfig;

class Username extends Validation
{
    /** @var string */
    protected $sTable;

    /** @var int */
    protected $iMin;

    /** @var int */
    protected $iMax;

    /**
     * @param string $sTable
     */
    public function __construct($sTable = DbTableName::MEMBER)
    {
        parent::__construct();

        $this->sTable = $sTable;
        $this->iMin = DbConfig::getSetting('minUsernameLength');
        $this->iMax = DbConfig::getSetting('maxUsernameLength');
        $this->message = t('Error: Nickname has to be from %0% to %1% characters long, or it is not available, or already used by other user.', $this->iMin, $this->iMax);
    }

    public function isValid($sValue)
    {
        return $this->oValidate->username($sValue, $this->iMin, $this->iMax, $this->sTable);
    }
}
