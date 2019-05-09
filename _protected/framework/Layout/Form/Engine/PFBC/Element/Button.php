<?php
/**
 * By Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

use PFBC\Element;

class Button extends Element
{
    /** @var array */
    protected $attributes;

    /** @var string */
    protected $icon;

    /**
     * @param string $sLabel
     * @param string $sType
     *
     * @param array|null $aProperties
     */
    public function __construct($sLabel = '', $sType = '', array $aProperties = null)
    {
        if (empty($sLabel)) {
            // Default translation value
            $sLabel = t('Submit');
        }

        $this->attributes = ['type' => 'submit', 'value' => t('Submit')];

        if (!is_array($aProperties)) {
            $aProperties = [];
        }

        if (!empty($sType)) {
            $aProperties['type'] = $sType;
        }

        if (empty($aProperties['value'])) {
            $aProperties['value'] = $sLabel;
        }

        parent::__construct($sLabel, '', $aProperties);
    }

    public function jQueryDocumentReady()
    {
        /*Unless explicitly prevented, jQueryUI's button widget functionality is applied to
        the each Button element.*/
        if (!in_array('jQueryUIButtons', $this->form->getPrevent(), true)) {
            echo 'jQuery("#', $this->attributes['id'], '").button(';
            /*Any of the jQueryUI framework icons can be added to your buttons via the icon
            property.  See http://jqueryui.com/themeroller/ for a complete list of available
            icons.*/
            if (!empty($this->icon)) {
                echo '{ icons: { primary: "ui-icon-', $this->icon, '" } }';
            }

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
