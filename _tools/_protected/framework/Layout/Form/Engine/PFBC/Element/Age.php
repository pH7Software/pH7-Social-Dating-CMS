<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Layout / Form / Engine / PFBC / Element
 */

namespace PFBC\Element;

use PFBC\OptionElement;
use PH7\Framework\Mvc\Model\DbConfig;

class Age extends OptionElement
{
    const MIN_AGE_TYPE = 'min_age';
    const MAX_AGE_TYPE = 'max_age';

    /** @var string */
    private $sHtmlOutput;

    /** @var int */
    private $iMinAge;

    /** @var int */
    private $iMaxAge;

    /**
     * Generate the select field for age search.
     *
     * @param array|null $aProperties
     */
    public function __construct($aProperties = null)
    {
        parent::__construct('', '', [], $aProperties);

        $this->iMinAge = (int)DbConfig::getSetting('minAgeRegistration');
        $this->iMaxAge = (int)DbConfig::getSetting('maxAgeRegistration');

        $sSelect1 = static::getOptions(static::MIN_AGE_TYPE);
        $sSelect2 = static::getOptions(static::MAX_AGE_TYPE);

        $this->sHtmlOutput = '<div class="pfbc-label"><label><strong>*</strong> ' . t('Age Range') . '</label></div>';
        $this->sHtmlOutput .= '<select name="age1">' . $sSelect1 . '</select> - <select name="age2">' . $sSelect2 . '</select>';
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

    /**
     * @param string $sType 'min_age' or 'max_age'
     *
     * @return string The age field with the default selected minimum and maximum age registration.
     */
    private function getOptions($sType)
    {
        $sSelect = '';

        for ($iAge = $this->iMinAge; $iAge <= $this->iMaxAge; $iAge++) {
            $sSelect .= '<option value="' . $iAge . '"';

            if ($this->isValueSelected($iAge, $sType)) {
                $sSelect .= ' selected="selected"';
            }

            $sSelect .= '>' . $iAge . '</option>';
        }

        return $sSelect;
    }

    /**
     * @param int $iAge
     * @param string $sType
     *
     * @return bool
     */
    private function isValueSelected($iAge, $sType)
    {
        $sAttrName = $sType === static::MIN_AGE_TYPE ? 'iMinAge' : 'iMaxAge';

        return (!empty($this->attributes['value'][$sType]) && $iAge == $this->attributes['value'][$sType]) ||
            (empty($this->attributes['value'][$sType]) && $iAge == $this->$sAttrName);
    }
}
