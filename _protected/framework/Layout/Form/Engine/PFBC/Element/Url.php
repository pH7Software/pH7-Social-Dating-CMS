<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Url extends Textbox
{

    public function render()
    {
        $this->attributes['type'] = 'url'; // URL type
        $this->validation[] = new \PFBC\Validation\Url;
        parent::render();
    }

}
