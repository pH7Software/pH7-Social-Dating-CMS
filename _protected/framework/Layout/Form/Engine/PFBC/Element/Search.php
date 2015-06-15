<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Search extends Textbox
{

    public function render()
    {
        $this->attributes['type'] = 'search'; // Search type
        parent::render();
    }

}
