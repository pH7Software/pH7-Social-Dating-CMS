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

        $this->sHtmlOutput = '<div class="pfbc-label"><label><strong>*</strong> ' . t('Age Range') . '</label></div>';

        $this->sHtmlOutput .= sprintf(
            '<input type="number" name="age1" placeholder="%d" min="%d" max="%d" />',
            $this->iMinAge + 5,
            $this->iMinAge,
            $this->iMaxAge - 1
        );

        $this->sHtmlOutput .= ' - ';

        $this->sHtmlOutput .= sprintf('<input type="number" name="age2" placeholder="%d" min="%d" max="%d" />',
            $this->iMaxAge - 10,
            $this->iMinAge + 1,
            $this->iMaxAge
        );
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }
}
