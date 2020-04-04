<?php
/**
 * @author Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

class Number extends Textbox
{
    public function render()
    {
        $this->attributes['type'] = 'number'; // Number Type
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();
    }
}
