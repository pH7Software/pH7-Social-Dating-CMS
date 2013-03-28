<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Button extends \PFBC\Element
{

    protected $attributes, $icon;

    public function __construct($sLabel = '', $sType = '', array $aProperties = null)
    {
        if(empty($sLabel)) $sLabel = t('Submit'); // Default translation value

        $this->attributes = array('type' => 'submit', 'value' => t('Submit'));

        if(!is_array($aProperties))
            $aProperties = array();

        if(!empty($sType))
            $aProperties['type'] = $sType;

        if(empty($aProperties['value']))
            $aProperties['value'] = $sLabel;

        parent::__construct($sLabel, '', $aProperties);
    }

    public function jQueryDocumentReady()
    {
        /*Unless explicitly prevented, jQueryUI's button widget functionality is applied to
        the each Button element.*/
        if(!in_array('jQueryUIButtons', $this->form->getPrevent())) {
            echo 'jQuery("#', $this->attributes['id'], '").button(';
            /*Any of the jQueryUI framework icons can be added to your buttons via the icon
            property.  See http://jqueryui.com/themeroller/ for a complete list of available
            icons.*/
            if(!empty($this->icon))
                echo '{ icons: { primary: "ui-icon-', $this->icon, '" } }';
            echo ');';
        }
    }

    public function render()
    {
        /*The button tag is used instead of input b/c it functions better with jQueryUI's
        button widget - specifically the icon option.*/
        echo '<button', $this->getAttributes(), '>', $this->label, '</button>';
    }

}
