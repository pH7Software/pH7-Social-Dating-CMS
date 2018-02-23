<?php
/**
 * File modified by Pierre-Henry Soria
 */

namespace PFBC\Element;

class TinyMCE extends Textarea
{
    protected $basic;

    public function jQueryDocumentReady()
    {
        echo 'jQuery("#', $this->attributes['id'], '").width(jQuery("#', $this->attributes['id'], '").width());';
    }

    public function renderJS()
    {
        echo <<<JS
tinyMCE.init({
    mode: "exact",
    elements: "{$this->attributes['id']}",
JS;
        if (empty($this->basic)) {
            echo <<<JS
    theme: "advanced",
    plugins: "safari,table,paste,inlinepopups,preview,fullscreen",
    dialog_type: "modal",
    theme_advanced_buttons1: "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,|,formatselect,fontselect,fontsizeselect",
    theme_advanced_buttons2: "link,unlink,anchor,image,charmap,hr,|,tablecontrols,|,pastetext,pasteword,|,cleanup,code,preview,fullscreen,|,undo,redo",
    theme_advanced_buttons3: "",
    theme_advanced_toolbar_location: "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_resizing : true,
JS;
        } else
            echo 'theme: "simple",';
        echo <<<JS
    forced_root_block: false,
    force_br_newlines: true,
    force_p_newlines: false
});
JS;

        $ajax = $this->form->getAjax();
        $id = $this->form->getID();
        if (!empty($ajax)) {
            echo <<<JS
    jQuery("#$id").bind("submit", function() {
        tinyMCE.triggerSave();
    });
JS;
        }
    }

    public function getJSFiles()
    {
        return [
            $this->form->getResourcesPath() . '/tiny_mce/tiny_mce.js'
        ];
    }
}
