<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
// JavaScript file is located in the directory ~static/js/str.js which is included in the file ~templates/themes/base/tpl/layout.tpl
namespace PFBC\Element;

class Textarea extends \PFBC\Element
{

    protected $attributes = array('class' => 'pfbc-textarea', 'rows' => '5');

    public function jQueryDocumentReady()
    {
        echo 'jQuery("#', $this->attributes['id'], '").outerWidth(jQuery("#', $this->attributes['id'], '").width());';
    }

    public function render()
    {
        echo '<textarea onkeyup="textCounter(\'', $this->attributes['id'], '\',\'', $this->attributes['id'], '_rem_len\')"', $this->getAttributes('value'), $sAttr, '>';
        if(!empty($this->attributes['value']))
            echo $this->filter($this->attributes['value']);

        $iLength = (!empty($this->attributes['value'])) ? (new \PH7\Framework\Str\Str)->length($this->attributes['value']) : '0';
        echo '</textarea><p><span id="', $this->attributes['id'], '_rem_len">' . $iLength . '</span> ', t('character(s).'), '</p>';
    }

}
