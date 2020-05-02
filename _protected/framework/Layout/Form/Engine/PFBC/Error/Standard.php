<?php
/**
 * We made many changes in this code.
 * By pH7.
 */

namespace PFBC\Error;

use PFBC\Error;
use PH7\Framework\Http\Http;

class Standard extends Error
{
    public function applyAjaxErrorResponse()
    {
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

    public function render()
    {
        $errors = $this->parse($this->form->getErrors());
        if (!empty($errors)) {
            $size = count($errors);
            echo '<div class="pfbc-error ui-state-error ui-corner-all">';
            echo nt('The following error was found:', 'The following errors were found:', $size);
            echo '<ul><li>', implode('</li><li>', $errors), '</li></ul></div>';
        }
    }

    private function parse($errors)
    {
        $list = [];
        if (!empty($errors)) {
            $keys = array_keys($errors);
            $keySize = sizeof($keys);
            for ($k = 0; $k < $keySize; ++$k) {
                $list = array_merge($list, $errors[$keys[$k]]);
            }
        }

        return $list;
    }

    public function renderAjaxErrorResponse()
    {
        $errors = $this->parse($this->form->getErrors());
        if (!empty($errors)) {
            Http::setContentType('application/json');
            echo json_encode(['errors' => $errors]);
        }
    }
}
