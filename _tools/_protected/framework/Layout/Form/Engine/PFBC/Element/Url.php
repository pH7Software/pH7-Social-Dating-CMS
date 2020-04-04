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
        $this->attributes['pattern'] = 'https?://.+'; // Accept only valid URL (browsers don't always validate it with just type="url")
        $this->validation[] = new \PFBC\Validation\Url;
        parent::render();
    }
}
