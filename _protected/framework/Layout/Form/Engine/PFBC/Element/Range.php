<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

use PFBC\Validation\Numeric;

class Range extends Textbox
{
    public function render()
    {
        $this->attributes += [
            'type' => 'range', // Range Type
            'id' => 'rangeInput',
            'oninput' => 'rangeOutput.value = rangeInput.value'
        ];
        $this->validation[] = new Numeric;
        parent::render();

        echo <<<'HTML'
            <strong><output id="rangeOutput"></output></strong>
            <script>$(function(){$("#rangeOutput").val($("#rangeInput").val())});</script>
HTML;
    }
}
