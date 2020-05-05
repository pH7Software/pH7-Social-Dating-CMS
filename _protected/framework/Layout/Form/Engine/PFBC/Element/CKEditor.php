<?php
/**
 * Some improvements were made by Pierre-Henry Soria
 */

namespace PFBC\Element;

class CKEditor extends Textarea
{
    protected $basic;

    public function renderJS()
    {
        echo 'CKEDITOR.replace("', $this->attributes["id"], '"';

        if (!empty($this->basic)) {
            echo ', { toolbar: "Basic" }';
        }

        echo ');';

        $ajax = $this->form->getAjax();
        $id = $this->form->getID();
        if (!empty($ajax)) {
            echo <<<JS
    jQuery("#$id").bind("submit", function() {
        CKEDITOR.instances["{$this->attributes["id"]}"].updateElement();
    });
JS;
        }
    }

    public function getJSFiles()
    {
        return [
            $this->form->getResourcesPath() . '/ckeditor/ckeditor.js'
        ];
    }
}
