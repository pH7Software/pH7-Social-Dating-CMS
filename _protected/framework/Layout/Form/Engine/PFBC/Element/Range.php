<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
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
