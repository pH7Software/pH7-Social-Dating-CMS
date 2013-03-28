<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC;

abstract class Error extends Base {
    protected $form;

    public function __construct(array $properties = null) {
        $this->configure($properties);
    }

    public abstract function applyAjaxErrorResponse();

    public function clear() {
        echo 'jQuery("#', $this->form->getId(), ' .pfbc-error").remove();';
    }

    public abstract function render();
    public abstract function renderAjaxErrorResponse();

    public function renderCSS() {
        $id = $this->form->getId();
        echo <<<CSS
#$id .pfbc-error{margin-top:6px;padding:.5em;margin-bottom:1em}
#$id .pfbc-error ul{padding-left:1.75em;margin:0;margin-top:.25em}
#$id .pfbc-error li{padding-top:.25em;display:list-item}
CSS;
    }

    public function setForm(Form $form) {
        $this->form = $form;
    }
}
