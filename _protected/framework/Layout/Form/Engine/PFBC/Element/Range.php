<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

class Range extends Textbox
{
    public function render()
    {
        // Get unique output/input ID name to prevent problems if the "range" field is used more than once on the same page
        $sOutputIdName = $this->getOutputIdName();
        $sRangeInputIdName = $this->getRangeInputName();

        $this->attributes['type'] = 'range'; // Range Type
        $this->attributes['id'] = $sRangeInputIdName;
        $this->attributes['oninput'] = $sOutputIdName . '.value = ' . $sRangeInputIdName . '.value';
        $this->validation[] = new \PFBC\Validation\Numeric;
        parent::render();

        echo '<strong>~ $<output id="' . $sOutputIdName . '" class="inline"></output></strong>';
        echo '<script>$(function(){$("#' . $sOutputIdName . '").val($("#' . $sRangeInputIdName . '").val())});</script>';
    }

    /**
     * @return string
     */
    private function getOutputIdName()
    {
        return 'rangeOutput' . mt_rand(1, 10) . '_';
    }


    /**
     * @return string
     */
    private function getRangeInputName()
    {
        return 'rangeInput' . mt_rand(1, 10) . '_';
    }
}
