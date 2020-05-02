<?php
/**
 * @author Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

use PFBC\Validation\Numeric;

class Number extends Textbox
{
    public function render()
    {
        $this->attributes['type'] = 'number'; // Number Type
        $this->validation[] = new Numeric;
        parent::render();
    }
}
