<?php
/**
 *  This file has been modified by pH7 developers team (Pierre-Henry SORIA).
 */

namespace PFBC;

abstract class View extends Base
{
    protected $form;

    public function __construct(array $properties = null)
    {
        $this->configure($properties);
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    /**
     * jQuery is used to apply css entries to the last element.
     */
    public function jQueryDocumentReady()
    {
        echo 'jQuery("#', $this->form->getId(), ' .pfbc-element:last").css({ "margin-bottom": "0", "padding-bottom": "0", "border-bottom": "none" });';
    }

    public function render()
    {
    }

    public function renderCSS()
    {
        $id = $this->form->getId();

        /*For ease-of-use, default styles are applied to form elements.*/
        if (!in_array('style', $this->form->getPrevent(), true)) {
            echo <<<CSS
#$id .pfbc-label label{font-weight:bold}
#$id em{font-size:.9em;color:#888}
#$id .pfbc-label strong{color:#990000}
#$id img.pfbc-loading{position:absolute;top:50%;right:.5em;margin-top:-8px;border:0}
CSS;
        }
    }

    public function renderJS()
    {
    }

    /**
     * This method encapsulates the various pieces that are included in an element's label.
     *
     * @param Element $element
     */
    protected function renderLabel(Element $element)
    {
        $label = $element->getLabel();
        $id = $element->getID();
        $description = $element->getDescription();

        if (!empty($label) || !empty($description)) {
            echo '<div class="pfbc-label">';
            if (!empty($label)) {
                echo '<label for="', $id, '">';

                if ($element->isRequired()) {
                    echo '<strong>*</strong> ';
                }
                echo $label, '</label>';
            }
            if (!empty($description)) {
                echo '<em>', $description, '</em>';
            }

            echo '</div>';
        }
    }
}
