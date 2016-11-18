<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Validation;
use PH7\Framework\Mvc\Model\DbConfig;

class Username extends \PFBC\Validation
{
    protected $sTable, $iMin, $iMax;

    /**
     * Constructor of class.
     *
     * @param string $sTable Default 'Members'
     */
    public function __construct($sTable = 'Members')
    {
        parent::__construct();

        $this->sTable = $sTable;
        $this->iMin = DbConfig::getSetting('minUsernameLength');
        $this->iMax = DbConfig::getSetting('maxUsernameLength');
        $this->message = t('Error: Your username has to contain from %0% to %1% characters, your username is not available or it is already used by other member.', $this->iMin, $this->iMax);
    }

    public function isValid($sValue)
    {
        return $this->oValidate->username($sValue, $this->iMin, $this->iMax, $this->sTable);
    }
}
