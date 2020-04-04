<?php
/**
 * By Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

use PH7\Framework\Security\Spam\Captcha\Captcha;

class CCaptcha extends Textbox
{
    public function render()
    {
        $this->attributes['required'] = 'required'; // Captcha field is always required!
        $this->attributes['autocomplete'] = 'off';

        $this->validation[] = new \PFBC\Validation\CCaptcha;
        echo (new Captcha)->display();
        parent::render();
    }
}
