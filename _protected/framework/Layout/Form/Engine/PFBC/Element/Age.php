<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
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
     * @param string $sLabel
     * @param array|null $aProperties
     */
    public function __construct($sLabel, array $aProperties = null)
    {
        parent::__construct($sLabel, '', [], $aProperties);

        $this->iMinAge = (int)DbConfig::getSetting('minAgeRegistration');
        $this->iMaxAge = (int)DbConfig::getSetting('maxAgeRegistration');

        $this->generateHtmlElements();
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

    private function generateHtmlElements()
    {
        $this->sHtmlOutput = sprintf(
            $this->minAgeHtmlField(),
            $this->minAgeDefaultValue(),
            $this->iMinAge + 5,
            $this->iMinAge,
            $this->iMaxAge - 1,
            $this->iMinAge,
            $this->iMinAge
        );

        $this->sHtmlOutput .= ' ~ ';

        $this->sHtmlOutput .= sprintf(
            $this->maxAgeHtmlField(),
            $this->maxAgeDefaultValue(),
            $this->iMaxAge - 10,
            $this->iMinAge + 1,
            $this->iMaxAge,
            $this->iMaxAge,
            $this->iMaxAge
        );
    }

    private function minAgeHtmlField()
    {
        return <<<'HTML'
        <input
            type="number"
            value="%d"
            name="min_age"
            placeholder="%d"
            min="%d"
            max="%d"
            onfocus="if('%d' == this.value) this.value = '';"
            onblur="if ('' == this.value) this.value = '%d';"
            required="required"
            />
HTML;
    }

    private function maxAgeHtmlField()
    {
        return <<<'HTML'
        <input
            type="number"
            value="%d"
            name="max_age"
            placeholder="%d"
            min="%d"
            max="%d"
            onfocus="if('%d' == this.value) this.value = '';"
            onblur="if ('' == this.value) this.value = '%d';"
            required="required"
            />
HTML;
    }

    /**
     * @return int
     */
    private function minAgeDefaultValue()
    {
        return !empty($this->attributes['value'][static::MIN_AGE_TYPE]) ? $this->attributes['value'][static::MIN_AGE_TYPE] : $this->iMinAge;
    }

    /**
     * @return int
     */
    private function maxAgeDefaultValue()
    {
        return !empty($this->attributes['value'][static::MAX_AGE_TYPE]) ? $this->attributes['value'][static::MAX_AGE_TYPE] : $this->iMaxAge;
    }
}
