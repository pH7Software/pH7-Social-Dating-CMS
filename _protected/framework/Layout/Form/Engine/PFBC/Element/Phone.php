<?php
/**
 * @author Pierre-Henry SORIA <http://ph7.me>
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
