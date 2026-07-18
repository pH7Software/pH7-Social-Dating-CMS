<?php

/**
 * Modified by Pierre-Henry Soria.
 */

namespace PFBC\Element;

use PFBC\Element;

class Textbox extends Element
{
    protected $attributes = ['type' => 'text', 'class' => 'pfbc-textbox'];

    public function render()
    {
        // Only for real text inputs: maxlength is meaningless/invalid on number, color, etc.
        // (subclasses like Number/Color/Email override the "type" attribute).
        if (($this->attributes['type'] ?? '') === 'text') {
            $this->applyMaxLengthFromValidation();
        }

        parent::render();
    }
}
