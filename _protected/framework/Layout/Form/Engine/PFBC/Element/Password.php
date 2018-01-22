<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class Password extends Textbox
{
    public function render()
    {
        // Adding the password type attribute
        $this->attributes['type'] = 'password';

        parent::render();
    }
}
