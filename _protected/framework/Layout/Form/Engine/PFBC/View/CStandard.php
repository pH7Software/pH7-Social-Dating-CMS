<?php
/**
 * By Pierre-Henry Soria <https://ph7.me>
 */

namespace PFBC\View;
// Class for pH7CMS

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\View;

class CStandard extends View
{
    public function render()
    {
        echo '<form', $this->form->getAttributes(), '>';
        $this->form->getError()->render();

        $elements = $this->form->getElements();
        $elementSize = count($elements);
        $elementCount = 0;

        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];

            if ($element instanceof Hidden || $element instanceof HTMLExternal)
                $element->render();
            elseif ($element instanceof Button) {
                if ($e == 0 || !$elements[($e - 1)] instanceof Button)
                    echo '<div class="pfbc-element pfbc-buttons">';
                $element->render();
                if (($e + 1) == $elementSize || !$elements[($e + 1)] instanceof Button)
                    echo '</div>';
            } else {
                echo '<div id="pfbc-element-', $elementCount, '">', $element->getPreHTML();
                $this->renderLabel($element);
                $element->render();
                echo $element->getPostHTML(), '</div>';
                ++$elementCount;
            }
        }

        echo '</form>';
    }

    public function renderCSS()
    {
        $id = $this->form->getId();
        $width = $this->form->getWidth();
        $widthSuffix = $this->form->getWidthSuffix();

        parent::renderCSS();
        echo <<<CSS
#$id { width: $width{$widthSuffix}; }
#$id .pfbc-element { margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #f4f4f4; }
#$id .pfbc-label { margin-bottom:.25em;padding-top:1.8em;}
#$id .pfbc-label label {display: block; }
#$id .pfbc-textbox, #$id .pfbc-textarea, #$id .pfbc-select { width: $width{$widthSuffix}; }
#$id .pfbc-buttons {text-align:right;padding-top:1.8em;}
CSS;

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;

        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];
            $elementWidth = $element->getWidth();
            if (!$element instanceof Hidden && !$element instanceof HTMLExternal && !$element instanceof HTMLExternal) {
                if (!empty($elementWidth)) {
                    echo '#', $id, ' #pfbc-element-', $elementCount, ' { width: ', $elementWidth, $widthSuffix, '; }';
                    echo '#', $id, ' #pfbc-element-', $elementCount, ' .pfbc-textbox, #', $id, ' #pfbc-element-', $elementCount, ' .pfbc-textarea, #', $id, ' #pfbc-element-', $elementCount, ' .pfbc-select { width: ', $elementWidth, $widthSuffix, '; }';
                }
                $elementCount++;
            }
        }
    }
}
