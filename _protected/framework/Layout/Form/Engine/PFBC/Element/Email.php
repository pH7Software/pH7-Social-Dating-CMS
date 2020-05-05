<?php
/**
 * Many changes have been made in this file.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class Email extends Textbox
{
    /** @var bool */
    private $bMailCheck;

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     * @param bool $bMailCheck
     */
    public function __construct($sLabel, $sName, array $aProperties = null, $bMailCheck = true)
    {
        $this->bMailCheck = $bMailCheck;
        parent::__construct($sLabel, $sName, $aProperties);
    }

    public function render()
    {
        $this->attributes['type'] = 'email'; // Email Type
        $this->validation[] = new \PFBC\Validation\Email;
        parent::render();
    }

    /**
     * @return array|void
     */
    public function getJSFiles()
    {
        if ($this->bMailCheck) {
            return [
                $this->form->getResourcesPath() . PH7_SH . PH7_JS . 'jquery/mailcheck.js',
                PH7_RELATIVE . 'asset/js/mailcheckConfig.js'
            ];
        }
    }
}
