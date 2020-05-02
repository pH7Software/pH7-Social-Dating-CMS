<?php
/**
 * We made some changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PFBC\OptionElement;

class Select extends OptionElement
{
    /** @var array */
    protected $attributes = ['class' => 'pfbc-select'];

    public function render()
    {
        if (isset($this->attributes['value'])) {
            if (!is_array($this->attributes['value'])) {
                $this->attributes['value'] = [$this->attributes['value']];
            }
        } else {
            $this->attributes['value'] = [];
        }

        if (!empty($this->attributes['multiple']) && substr($this->attributes['name'], -2) !== '[]') {
            $this->attributes['name'] .= '[]';
        }

        echo '<select', $this->getAttributes(['value', 'selected']), $this->getHtmlRequiredIfApplicable(), '>';
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            echo '<option value="', $this->filter($value), '"';

            if (in_array($value, $this->attributes['value'], false)) {
                echo ' selected="selected"';
            }
            echo '>', $text, '</option>';
        }
        echo '</select>';
    }
}
