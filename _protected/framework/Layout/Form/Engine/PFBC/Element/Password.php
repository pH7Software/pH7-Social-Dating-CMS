<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Mvc\Model\DbConfig;

class Password extends Textbox
{

    public function render()
    {
        // Adding the password pattern
        $this->attributes['pattern'] = '.{'.DbConfig::getSetting('minPasswordLength').','.DbConfig::getSetting('maxPasswordLength').'}';
        // Adding the password type attribute
        $this->attributes['type'] = 'password';

        parent::render();
    }

}
