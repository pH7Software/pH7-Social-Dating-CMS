<?php
/**
 * We made many changes in this code.
 * By pH7.
 */
namespace PFBC\Error;

class Standard extends \PFBC\Error {
    public function applyAjaxErrorResponse() {
        $id = $this->form->getId();
        echo <<<JS
var errorSize = response.errors.length;
if(errorSize == 1)
    var errorFormat = "error was";
else
    var errorFormat = errorSize + " errors were";

var errorHTML = '<div class="pfbc-error ui-state-error ui-corner-all">The following ' + errorFormat + ' found:<ul>';
for(e = 0; e < errorSize; ++e)
    errorHTML += '<li>' + response.errors[e] + '</li>';
errorHTML += '</ul></div>';
jQuery("#$id").prepend(errorHTML);
JS;

    }

    private function parse($errors) {
        $list = array();
        if(!empty($errors)) {
            $keys = array_keys($errors);
            $keySize = sizeof($keys);
            for($k = 0; $k < $keySize; ++$k)
                $list = array_merge($list, $errors[$keys[$k]]);
        }
        return $list;
    }

    public function render() {
        $errors = $this->parse($this->form->getErrors());
        if(!empty($errors)) {
            $size = sizeof($errors);
            if($size == 1)
                $format = "error was";
            else
                $format = $size . " errors were";

            echo '<div class="pfbc-error ui-state-error ui-corner-all">The following ', $format, ' found:<ul><li>', implode('</li><li>', $errors), '</li></ul></div>';
        }
    }

    public function renderAjaxErrorResponse() {
        $errors = $this->parse($this->form->getErrors());
        if(!empty($errors)) {
            \PH7\Framework\Http\Http::setContentType('application/json');
            echo json_encode(array('errors' => $errors));
        }
    }
}
