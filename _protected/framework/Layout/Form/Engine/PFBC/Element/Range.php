<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Range extends Textbox
{

    public function render()
    {
        $this->attributes['type'] = 'range'; // Range Type
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();
    }

}
