<?php
/**
 * Changes made in this code from original PFBC's version.
 * By Pierre-Henry Soria <https://ph7.me>
 */

namespace PFBC\View;

use PFBC\Element\Button;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\View;

class SideBySide extends View
{
    protected $labelWidth;
    protected $labelRightAlign;
    protected $labelPaddingRight = 5;
    protected $labelPaddingTop;

    public function __construct($labelWidth, array $properties = null)
    {
        if (!empty($properties)) {
            $properties['labelWidth'] = $labelWidth;
        } else {
            $properties = ['labelWidth' => $labelWidth];
        }

        parent::__construct($properties);
    }

    public function render()
    {
        echo '<form', $this->form->getAttributes(), '>';
        $this->form->getError()->render();

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
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
                echo '<div id="pfbc-element-', $elementCount, '" class="pfbc-element">', $element->getPreHTML();
                $this->renderLabel($element);
                echo '<div class="pfbc-right">';
                $element->render();
                echo '</div><div style="clear: both;"></div>', $element->getPostHTML(), '</div>';
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

        if ($widthSuffix === 'px') {
            $elementWidth = $width - $this->labelWidth - $this->labelPaddingRight;
        } else {
            $elementWidth = 100 - $this->labelWidth - $this->labelPaddingRight;
        }

        View::renderCSS();
        echo <<<CSS
#$id { width: $width{$widthSuffix}; }
#$id .pfbc-element { margin-bottom: 1em; padding-bottom: 1em; border-bottom: 1px solid #f4f4f4; }
#$id .pfbc-label { width: {$this->labelWidth}$widthSuffix; float: left; padding-right: {$this->labelPaddingRight}$widthSuffix; }
#$id .pfbc-buttons { text-align: right; }
#$id .pfbc-textbox, #$id .pfbc-textarea, #$id .pfbc-select, #$id .pfbc-right { width: $elementWidth{$widthSuffix}; }
#$id .pfbc-right { float: right; }
CSS;

        if (!empty($this->labelRightAlign))
            echo '#', $id, ' .pfbc-label { text-align: right; }';

        if (empty($this->labelPaddingTop) && !in_array('style', $this->form->getPrevent(), true)) {
            $this->labelPaddingTop = '.75em';
        }

        if (!empty($this->labelPaddingTop)) {
            if (is_numeric($this->labelPaddingTop))
                $this->labelPaddingTop .= 'px';
            echo '#', $id, ' .pfbc-label { padding-top: ', $this->labelPaddingTop, '; }';
        }

        $elements = $this->form->getElements();
        $elementSize = sizeof($elements);
        $elementCount = 0;
        for ($e = 0; $e < $elementSize; ++$e) {
            $element = $elements[$e];
            $elementWidth = $element->getWidth();
            if (!$element instanceof Hidden && !$element instanceof HTMLExternal && !$element instanceof HTMLExternal) {
                if (!empty($elementWidth)) {
                    echo '#', $id, ' #pfbc-element-', $elementCount, ' { width: ', $elementWidth, $widthSuffix, '; }';
                    if ($widthSuffix === 'px') {
                        $elementWidth = $elementWidth - $this->labelWidth - $this->labelPaddingRight;
                        echo '#', $id, ' #pfbc-element-', $elementCount, ' .pfbc-textbox, #', $id, ' #pfbc-element-', $elementCount, ' .pfbc-textarea, #', $id, ' #pfbc-element-', $elementCount, ' .pfbc-select, #', $id, ' #pfbc-element-', $elementCount, ' .pfbc-right { width: ', $elementWidth, $widthSuffix, '; }';
                    }
                }
                $elementCount++;
            }
        }
    }
}
