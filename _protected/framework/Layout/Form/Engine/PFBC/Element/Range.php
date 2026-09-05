<?php

/**
 * File created by Pierre-Henry Soria <hi@ph7.me>.
 */

namespace PFBC\Element;

use PFBC\Validation\Numeric;
use PH7\Framework\Str\Str;

class Range extends Textbox
{
    public function render()
    {
        $this->attributes += [
            'type' => 'range', // Range Type
            'id' => 'rangeInput'
        ];
        $this->validation[] = new Numeric();
        parent::render();

        $sInputId = (new Str())->escapeAttribute($this->attributes['id']);
        echo '<strong><output id="', $sInputId, '_output" for="', $sInputId, '"></output></strong>';
    }

    public function jQueryDocumentReady()
    {
        $sInputId = json_encode($this->attributes['id'], JSON_HEX_TAG);
        $sOutputId = json_encode($this->attributes['id'] . '_output', JSON_HEX_TAG);

        echo <<<JS
        (function() {
            var oInput = document.getElementById($sInputId);
            var oOutput = document.getElementById($sOutputId);
            oOutput.value = oInput.value;
            jQuery(oInput).on("input", function() {
                oOutput.value = this.value;
            });
        })();
JS;
    }
}
