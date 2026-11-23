<?php
/**
 * File modified by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

class Checksort extends Sort
{
    private const JAVASCRIPT_JSON_FLAGS = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP |
        JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR;

    protected $attributes = ['type' => 'checkbox'];
    protected $inline;
    protected $maxheight;

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();
        if (!empty($this->inline)) {
            echo 'jQuery("#', $this->attributes['id'], ' .pfbc-checkbox:last").css("margin-right", "0");';
        } else {
            echo 'jQuery("#', $this->attributes['id'], ' .pfbc-checkbox:last").css({ "padding-bottom": "0", "border-bottom": "none" });';
        }

        if (!empty($this->maxheight) && is_numeric($this->maxheight)) {
            echo <<<JS
var checkboxes = jQuery("#{$this->attributes['id']} .pfbc-checkboxes");
if(checkboxes.outerHeight() > {$this->maxheight}) {
    checkboxes.css({
        "height": "{$this->maxheight}px",
        "overflow": "auto",
        "overflow-x": "hidden"
    });
}
JS;
        }
    }

    public function render()
    {
        if (isset($this->attributes['value'])) {
            if (!is_array($this->attributes['value'])) {
                $this->attributes['value'] = [$this->attributes['value']];
            }
        } else {
            $this->attributes['value'] = [];
        }

        if (substr($this->attributes['name'], -2) !== '[]') {
            $this->attributes['name'] .= '[]';
        }

        $count = 0;
        $existing = '';
        echo '<div id="', $this->attributes['id'], '"><div class="pfbc-checkboxes">';
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            $sFilteredText = $this->filter($text);
            $sOnClick = 'updateChecksort(this, ' . json_encode(
                (string)$text,
                self::JAVASCRIPT_JSON_FLAGS
            ) . ');';
            echo '<div class="pfbc-checkbox"><table cellpadding="0" cellspacing="0"><tr><td style="vertical-align:top"><input id="', $this->attributes['id'], "-", $count, '"', $this->getAttributes(["id", "value", "checked", "name", "onclick"]), ' value="', $this->filter($value), '"';
            if (in_array($value, $this->attributes['value'], false)) {
                echo ' checked="checked"';
            }

            echo ' onclick="', $this->filter($sOnClick), '"/></td><td><label for="', $this->attributes['id'], '-', $count, '">', $sFilteredText, '</label></td></tr></table></div>';

            if (in_array($value, $this->attributes['value'], false)) {
                $existing .= '<li id="' . $this->attributes['id'] . '-sort-' . $count . '" class="ui-state-default"><input type="hidden" name="' .
                    $this->filter($this->attributes['name']) . '" value="' . $this->filter($value) . '"/>' . $sFilteredText . '</li>';
            }

            ++$count;
        }
        echo '</div>';

        if (!empty($this->inline)) {
            echo '<div style="clear: both;"></div>';
        }

        echo '<ul>', $existing, '</ul></div>';
    }

    public function renderJS()
    {
        $sName = json_encode(
            (string)$this->attributes['name'],
            self::JAVASCRIPT_JSON_FLAGS
        );

        echo <<<JS
if(typeof updateChecksort !== 'function') {
    function updateChecksort(element, text) {
        var position = element.id.lastIndexOf("-");
        var id = element.id.substr(0, position);
        var index = element.id.substr(position + 1);
        if(element.checked) {
            var listItem = document.createElement("li");
            var input = document.createElement("input");
            listItem.id = id + "-sort-" + index;
            listItem.className = "ui-state-default";
            input.type = "hidden";
            input.name = {$sName};
            input.value = element.value;
            listItem.appendChild(input);
            listItem.appendChild(document.createTextNode(text));
            document.getElementById(id).querySelector("ul").appendChild(listItem);
        } else {
            var existingItem = document.getElementById(id + "-sort-" + index);
            if (existingItem) {
                existingItem.parentNode.removeChild(existingItem);
            }
        }
    }
}
JS;
    }

    public function renderCSS()
    {
        if (!empty($this->inline)) {
            echo '#', $this->attributes['id'], ' .pfbc-checkbox { float: left; margin-right: 0.5em; }';
        } else {
            echo '#', $this->attributes['id'], ' .pfbc-checkbox { padding: 0.5em 0; border-bottom: 1px solid #f4f4f4; }';
        }

        parent::renderCSS();
    }
}
