<?php
/**
 * File modified by Pierre-Henry Soria
 */

namespace PFBC\Element;

use PFBC\OptionElement;

class Sort extends OptionElement
{
    protected $jQueryOptions;

    public function jQueryDocumentReady()
    {
        echo 'jQuery("#', $this->attributes['id'], ' ul").sortable(', $this->jQueryOptions(), ');';
        echo 'jQuery("#', $this->attributes['id'], ' ul").disableSelection();';
    }

    public function render()
    {
        if (substr($this->attributes['name'], -2) !== '[]') {
            $this->attributes['name'] .= '[]';
        }

        echo '<div id="', $this->attributes['id'], '"><ul>';
        foreach ($this->options as $value => $text) {
            $value = $this->getOptionValue($value);
            echo '<li class="ui-state-default"><input type="hidden" name="', $this->attributes['name'], '" value="', $value, '"/>', $text, '</li>';
        }
        echo "</ul></div>";
    }

    public function renderCSS()
    {
        echo '#', $this->attributes['id'], ' ul { list-style-type: none; margin: 0; padding: 0; cursor: pointer; }';
        echo '#', $this->attributes['id'], ' ul li { margin: 0.25em 0; padding: 0.5em; font-size: 1em; }';
    }
}
