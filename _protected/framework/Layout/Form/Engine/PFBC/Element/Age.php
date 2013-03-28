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
    public function __construct()
    {
        $this->iMinAge = DbConfig::getSetting('minAgeRegistration');
        $this->iMaxAge = DbConfig::getSetting('maxAgeRegistration');

        // Default values
        $sSelect1 = '';
        $sSelect2 = '';

        for($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++)
        {
            if($iAge == $this->iMinAge)
                 $sSelect1 .= '<option value="' . $iAge . '" selected="selected">' . $iAge . '</option>';

            $sSelect1 .= '<option value="' . $iAge . '">' . $iAge . '</option>';
        }

        for($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++)
        {
            if($iAge == $this->iMaxAge)
                $sSelect2 .= '<option value="' . $iAge . '" selected="selected">' . $iAge . '</option>';

            $sSelect2 .= '<option value="' . $iAge . '">' . $iAge . '</option>';
        }

        $this->sHtmlOutput = '<div class="pfbc-label"><label><strong>*</strong>' . t('Age') . '</label></div><select name="age1">' . $sSelect1 . '</select> - <select name="age2">' . $sSelect2 . '</select> &nbsp; ' . t('years');
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

}
