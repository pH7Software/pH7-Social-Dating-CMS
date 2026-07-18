<?php

/**
 * Changes made by Pierre-Henry Soria.
 */

// JavaScript file is located in the directory ~static/js/str.js which is included in the file ~templates/themes/base/tpl/layout.tpl

namespace PFBC\Element;

use PFBC\Element;
use PFBC\Validation\Str as StrValidation;
use PH7\Framework\Str\Str;

class Textarea extends Element
{
    /** @var array */
    protected $attributes = ['class' => 'pfbc-textarea'];

    public function render()
    {
        $iLength = !empty($this->attributes['value']) ? (new Str())->length($this->attributes['value']) : 0;

        // If no explicit maxlength was given, derive one from a Str(min, max) validator when present,
        // so the counter can show the limit and the browser enforces it — without per-form edits.
        if (empty($this->attributes['maxlength'])) {
            $iValidatorMax = $this->getValidationMaxLength();
            if ($iValidatorMax !== null) {
                $this->attributes['maxlength'] = $iValidatorMax;
            }
        }

        echo '<textarea onkeyup="textCounter(\'', $this->attributes['id'], '\',\'', $this->attributes['id'], '_rem_len\')"', $this->getAttributes('value'), $this->getHtmlRequiredIfApplicable(), '>';

        if (!empty($this->attributes['value'])) {
            echo $this->filter($this->attributes['value']);
        }

        // When a maxlength is set, show "typed / max" so the limit is visible; otherwise just the count.
        $sMaxSuffix = !empty($this->attributes['maxlength']) ? ' / ' . (int)$this->attributes['maxlength'] : '';

        echo '</textarea><p><span id="', $this->attributes['id'], '_rem_len">' . $iLength . '</span>' . $sMaxSuffix . ' ', t('character(s).'), '</p>';
    }

    /**
     * @return int|null the max length declared by a Str validator on this element, or NULL if none
     */
    private function getValidationMaxLength(): ?int
    {
        if (!empty($this->validation)) {
            foreach ($this->validation as $oValidation) {
                if ($oValidation instanceof StrValidation) {
                    return $oValidation->getMax();
                }
            }
        }

        return null;
    }
}
