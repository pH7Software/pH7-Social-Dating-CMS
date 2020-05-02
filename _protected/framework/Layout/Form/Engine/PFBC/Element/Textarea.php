<?php
/**
 * Changes made by Pierre-Henry Soria.
 */

// JavaScript file is located in the directory ~static/js/str.js which is included in the file ~templates/themes/base/tpl/layout.tpl
namespace PFBC\Element;

use PFBC\Element;
use PH7\Framework\Str\Str;

class Textarea extends Element
{
    /** @var array */
    protected $attributes = ['class' => 'pfbc-textarea'];

    public function render()
    {
        $iLength = !empty($this->attributes['value']) ? (new Str)->length($this->attributes['value']) : 0;

        echo '<textarea onkeyup="textCounter(\'', $this->attributes['id'], '\',\'', $this->attributes['id'], '_rem_len\')"', $this->getAttributes('value'), $this->getHtmlRequiredIfApplicable(), '>';

        if (!empty($this->attributes['value'])) {
            echo $this->filter($this->attributes['value']);
        }

        echo '</textarea><p><span id="', $this->attributes['id'], '_rem_len">' . $iLength . '</span> ', t('character(s).'), '</p>';
    }
}
