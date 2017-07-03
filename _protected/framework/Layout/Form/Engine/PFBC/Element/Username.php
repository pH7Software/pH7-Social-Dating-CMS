<?php
/**
 * By Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

use PH7\Framework\Mvc\Model\DbConfig;

class Username extends Textbox
{
    public function render()
    {
        // Adding the username pattern
        $this->attributes['pattern'] = PH7_USERNAME_PATTERN . '{' . DbConfig::getSetting('minUsernameLength') . ',' . DbConfig::getSetting('maxUsernameLength') . '}';
        parent::render();
    }
}
