<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;
use \PH7\Framework\Security\Spam\Captcha\Captcha;

class CCaptcha extends Textbox
{

    public function render()
    {
        $this->attributes['required'] = 'required'; // Captcha field is always required!

        $this->validation[] = new \PFBC\Validation\CCaptcha;
        echo (new Captcha)->display();
        parent::render();
    }

}
