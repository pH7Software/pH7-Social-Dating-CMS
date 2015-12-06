<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Mvc\Model\DbConfig;

class Age extends \PFBC\OptionElement
{

    protected $sHtmlOutput, $iMinAge, $iMaxAge;

    /**
     * Generate the select field for age search.
     *
     * @return The field age with the default selected minimum and maximum registration age.
     */
    public function __construct($aProperties = null)
    {
        parent::__construct('', '', array(), $aProperties);

        $this->iMinAge = DbConfig::getSetting('minAgeRegistration');
        $this->iMaxAge = DbConfig::getSetting('maxAgeRegistration');

        // Default values
        $sSelect1 = '';
        $sSelect2 = '';

        for ($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++)
        {
            $sSelect1 .= '<option value="' . $iAge . '"';

            if (!empty($this->attributes['value']['min_age']) && $iAge == $this->attributes['value']['min_age']
                || empty($this->attributes['value']['min_age']) && $iAge == $this->iMinAge
            )
            {
                $sSelect1 .= ' selected="selected"';
            }

            $sSelect1 .= '>' . $iAge . '</option>';
        }

        for ($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++)
        {
            $sSelect2 .= '<option value="' . $iAge . '"';

            if (!empty($this->attributes['value']['max_age']) && $iAge == $this->attributes['value']['max_age']
                || empty($this->attributes['value']['max_age']) && $iAge == $this->iMaxAge
            )
            {
                $sSelect2 .= ' selected="selected"';
            }

            $sSelect2 .= '>' . $iAge . '</option>';
        }

        $this->sHtmlOutput = '<div class="pfbc-label"><label><strong>*</strong>' . t('Age') . '</label></div><select name="age1">' . $sSelect1 . '</select> - <select name="age2">' . $sSelect2 . '</select> &nbsp; ' . t('years');
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

}
