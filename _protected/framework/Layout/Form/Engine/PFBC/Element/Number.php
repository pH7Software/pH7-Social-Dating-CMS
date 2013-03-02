<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
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
