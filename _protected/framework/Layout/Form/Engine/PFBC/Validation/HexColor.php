<?php
/**
 * Created by Pierre-Henry Soria
 */

namespace PFBC\Validation;

class HexColor extends \PFBC\Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: The HEX color value is invalid. Has to be "#XXXXXX" format.');
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->hex($sValue);
    }
}
