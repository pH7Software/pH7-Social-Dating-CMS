<?php
/**
 * We made some changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Select extends \PFBC\OptionElement
{

    protected $attributes = array('class' => 'pfbc-select');

    public function render()
    {
        if (isset($this->attributes['value']))
        {
            if (!is_array($this->attributes['value']))
                $this->attributes['value'] = array($this->attributes['value']);
        }
        else
            $this->attributes['value'] = array();

        if (!empty($this->attributes['multiple']) && substr($this->attributes['name'], -2) != '[]')
            $this->attributes['name'] .= '[]';

        echo '<select', $this->getAttributes(array('value', 'selected')), $this->getHtmlRequiredIfApplicable(), '>';
        foreach ($this->options as $value => $text)
        {
            $value = $this->getOptionValue($value);
            echo '<option value="', $this->filter($value), '"';
            $selected = false;
            if (in_array($value, $this->attributes['value']))
                echo ' selected="selected"';
            echo '>', $text, '</option>';
        }
        echo '</select>';
    }

}
