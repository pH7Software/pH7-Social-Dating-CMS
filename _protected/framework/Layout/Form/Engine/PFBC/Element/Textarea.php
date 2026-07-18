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
        $iLength = !empty($this->attributes['value']) ? (new Str())->length($this->attributes['value']) : 0;

        // Derive maxlength from a Str validator so the counter shows the limit and the browser enforces it.
        $this->applyMaxLengthFromValidation();

        echo '<textarea onkeyup="textCounter(\'', $this->attributes['id'], '\',\'', $this->attributes['id'], '_rem_len\')"', $this->getAttributes('value'), $this->getHtmlRequiredIfApplicable(), '>';

        if (!empty($this->attributes['value'])) {
            echo $this->filter($this->attributes['value']);
        }

        // When a maxlength is set, show "typed / max" so the limit is visible; otherwise just the count.
        $sMaxSuffix = !empty($this->attributes['maxlength']) ? ' / ' . (int)$this->attributes['maxlength'] : '';

        echo '</textarea><p><span id="', $this->attributes['id'], '_rem_len">' . $iLength . '</span>' . $sMaxSuffix . ' ', t('character(s).'), '</p>';
    }
}
