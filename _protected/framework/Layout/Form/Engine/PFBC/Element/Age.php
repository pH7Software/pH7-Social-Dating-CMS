<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Layout / Form / Engine / PFBC / Element
 */
namespace PFBC\Element;

use PH7\Framework\Mvc\Model\DbConfig;

class Age extends \PFBC\OptionElement
{

    const MIN_AGE = 'min_age', MAX_AGE = 'max_age';

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

        $sSelect1 = static::getOptions(static::MIN_AGE);
        $sSelect2 = static::getOptions(static::MAX_AGE);

        $this->sHtmlOutput = '<div class="pfbc-label"><label><strong>*</strong>' . t('Age') . '</label></div><select name="age1">' . $sSelect1 . '</select> - <select name="age2">' . $sSelect2 . '</select> &nbsp; ' . t('years');
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

    /**
     * @param string $sType 'min_age' or 'max_age'
     */
    protected function getOptions($sType)
    {
        $sSelect = '';
        $sAttrName = ($sType == static::MIN_AGE) ? 'iMinAge' : 'iMaxAge';

        for ($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++)
        {
            $sSelect .= '<option value="' . $iAge . '"';

            if (!empty($this->attributes['value'][$sType]) && $iAge == $this->attributes['value'][$sType]
                || empty($this->attributes['value'][$sType]) && $iAge == $this->$sAttrName
            )
            {
                $sSelect .= ' selected="selected"';
            }

            $sSelect .= '>' . $iAge . '</option>';
        }

        return $sSelect;
    }

}
