<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use PH7\Framework\Config\Config;

class Phone extends Textbox
{

    public function render()
    {
        $this->attributes['type'] = 'tel'; // Phone type
        $this->attributes['pattern'] = Config::getInstance()->values['validate']['phone.pattern'];
        $this->validation[] = new \PFBC\Validation\Phone;
        parent::render();
    }

}
