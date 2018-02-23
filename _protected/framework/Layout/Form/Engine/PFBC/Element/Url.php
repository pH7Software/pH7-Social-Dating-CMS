<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
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
