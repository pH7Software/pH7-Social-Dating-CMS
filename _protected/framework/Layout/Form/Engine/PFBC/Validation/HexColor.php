<?php
/**
 * Created by Pierre-Henry Soria
 */

namespace PFBC\Validation;

use PFBC\Validation;

class HexColor extends Validation
{
    public function __construct()
    {
        parent::__construct();
        $this->message = t('Error: The HEX color value is invalid. Has to be "#XXXXXX" format.<br /> Maybe your browser is outdated or the version does not support "color" input type.<br /> Please retry with another browser such as Firefox, Chrome, Brave.');
    }

    public function isValid($sValue)
    {
        return $this->isNotApplicable($sValue) || $this->oValidate->hex($sValue);
    }
}
