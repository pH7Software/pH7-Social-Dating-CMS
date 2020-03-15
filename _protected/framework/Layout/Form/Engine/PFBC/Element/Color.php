<?php
/**
 * I made changes in this file (by Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PFBC\Validation\HexColor;

class Color extends Textbox
{
    public function render()
    {
        $this->attributes['type'] = 'color';
        $this->validation[] = new HexColor;
        parent::render();
    }
}
