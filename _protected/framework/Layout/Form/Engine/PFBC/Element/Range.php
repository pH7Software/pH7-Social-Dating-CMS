<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

class Range extends Textbox
{
    public function render()
    {
        $this->attributes['type'] = 'range'; // Range Type
        $this->attributes['id'] = 'rangeInput';
        $this->attributes['oninput'] = 'rangeOutput.value = rangeInput.value';
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();

        echo <<<'HTML'
            <strong><output id="rangeOutput"></output></strong>
            <script>$(function(){$("#rangeOutput").val($("#rangeInput").val())});</script>
HTML;
    }
}
