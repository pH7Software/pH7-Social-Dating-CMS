<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Email extends Textbox
{

    public function render()
    {
        $this->attributes['type'] = 'email'; // Email Type
        $this->validation[] = new \PFBC\Validation\Email;
        parent::render();

        echo '<script src="' . (new \PFBC\Form)->getResourcesPath() . PH7_SH . PH7_JS . 'jquery/mailcheck.js"></script><script src=' . PH7_RELATIVE . 'asset/js/mailcheck.js></script>';
    }

}
