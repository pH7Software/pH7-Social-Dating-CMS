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
        $this->attributes['type'] = 'search'; // Search Type
        $this->attributes['x-webkit-speech'] = 'x-webkit-speech'; // Voice search. Only for Webkit engine.
        parent::render();
    }

}
