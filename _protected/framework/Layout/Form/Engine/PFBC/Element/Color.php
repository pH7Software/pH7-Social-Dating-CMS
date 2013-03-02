<?php
namespace PFBC\Element;

class Color extends Textbox
{

    protected $jQueryOptions;

    public function jQueryDocumentReady()
    {
        parent::jQueryDocumentReady();

        if(empty($this->jQueryOptions["onSubmit"]))
            $this->jQueryOptions["onSubmit"] = 'js:function(hsb, hex, rgb, el) { jQuery(el).val("#" + hex); jQuery(el).ColorPickerHide(); }';
        if(empty($this->jQueryOptions["onBeforeShow"]))
            $this->jQueryOptions["onBeforeShow"] = 'js:function() { jQuery(this).ColorPickerSetColor(this.value); }';

        echo 'jQuery("#', $this->attributes["id"], '").ColorPicker(', $this->jQueryOptions(), ').bind("keyup", function() { jQuery(this).ColorPickerSetColor(this.value); });';
    }

    function getCSSFiles()
    {
        return array(
            $this->form->getResourcesPath() . "/colorpicker/css/colorpicker.css"
        );
    }

    function getJSFiles()
    {
        return array(
            $this->form->getResourcesPath() . "/colorpicker/js/colorpicker.js"
        );
    }

}
