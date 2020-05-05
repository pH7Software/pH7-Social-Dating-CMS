<?php
/**
 * Many changes have been made in this file.
 * By Pierre-Henry SORIA.
 */

namespace PFBC;

use PFBC\Element\CKEditor;

abstract class Element extends Base
{
    /** @var array */
    protected $attributes;

    /** @var Form */
    protected $form;

    /** @var string */
    protected $label;

    protected $description;
    protected $validation = [];
    protected $preHTML;
    protected $postHTML;
    protected $width;
    private $errors = [];

    public function __construct($label, $name, array $properties = null)
    {
        $configuration = [
            'label' => $label,
            'name' => $name
        ];

        /*Merge any properties provided with an associative array containing the label
        and name properties.*/
        if (is_array($properties)) {
            $configuration = array_merge($configuration, $properties);
        }

        $this->configure($configuration);
    }

    /**
     * When an element is serialized and stored in the session, this method prevents any non-essential
     * information from being included.
     */
    public function __sleep()
    {
        return ['attributes', 'label', 'validation'];
    }

    /**
     * If an element requires external stylesheets, this method is used to return an
     * array of entries that will be applied before the form is rendered.
     */
    public function getCSSFiles()
    {
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getID()
    {
        if (!empty($this->attributes['id'])) {
            return $this->attributes['id'];
        }

        return '';
    }

    public function getJSFiles()
    {
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getName()
    {
        if (!empty($this->attributes['name'])) {
            return $this->attributes['name'];
        }

        return '';
    }

    public function getPostHTML()
    {
        return $this->postHTML;
    }

    public function getPreHTML()
    {
        return $this->preHTML;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function setWidth($width)
    {
        if (substr($width, -2) === 'px') {
            $width = substr($width, 0, -2);
        } elseif (substr($width, -1) === '%') {
            $width = substr($width, 0, -1);
        }

        $this->width = $width;
    }

    /**
     * The method ensures that the provided value satisfies each of the
     * element's validation rules.
     *
     * @param string $value
     *
     * @return bool
     */
    public function isValid($value)
    {
        $valid = true;
        if (!empty($this->validation)) {
            if (!empty($this->label)) {
                $element = $this->label;
                if (substr($element, -1) === ':') {
                    $element = substr($element, 0, -1);
                }
            } else {
                $element = $this->attributes['name'];
            }

            foreach ($this->validation as $validation) {
                if (!$validation->isValid($value)) {
                    /*In the error message, %element% will be replaced by the element's label (or
                    name if label is not provided).*/
                    $this->errors[] = str_replace('%element%', $element, $validation->getMessage());
                    $valid = false;
                }
            }
        }

        return $valid;
    }

    /**
     * If an element requires jQuery, this method is used to include a section of javascript
     * that will be applied within the jQuery(document).ready(function() {}); section after the
     * form has been rendered.
     */
    public function jQueryDocumentReady()
    {
    }

    /**
     * Elements that have the jQueryOptions property included (Date, Sort, Checksort, and Color)
     * can make use of this method to render out the element's appropriate jQuery options.
     */
    public function jQueryOptions()
    {
        if (!empty($this->jQueryOptions)) {
            $options = '';
            foreach ($this->jQueryOptions as $option => $value) {
                if (!empty($options)) {
                    $options .= ', ';
                }
                $options .= $option . ': ';
                /*When javascript needs to be applied as a jQuery option's value, no quotes are needed.*/
                if (is_string($value) && substr($value, 0, 3) === 'js:') {
                    $options .= substr($value, 3);
                } else {
                    $options .= var_export($value, true);
                }
            }
            echo '{ ', $options, ' }';
        }
    }

    /**
     * Many of the included elements make use of the <input> tag for display.  These include the Hidden, Textbox,
     * Password, Date, Color, Button, Email, and File element classes.  The project's other element classes will
     * override this method with their own implementation.
     */
    public function render()
    {
        if (isset($this->attributes['value']) && is_array($this->attributes['value'])) {
            $this->attributes['value'] = '';
        }

        $sHtml = '<input' . $this->getAttributes();
        $sHtml .= $this->getHtmlRequiredIfApplicable();
        echo $sHtml, ' />';
    }

    protected function getHtmlRequiredIfApplicable()
    {
        $sCode = '';

        // 'required' attr won't work with CKEditor editor, so ignore it if class called by 'CKEditor'
        if (static::class !== CKEditor::class && $this->isRequired()) {
            $sCode .= ' required="required"';
        }

        return $sCode;
    }

    /**
     * This method provides a shortcut for checking if an element is required.
     */
    public function isRequired()
    {
        if (!empty($this->validation)) {
            foreach ($this->validation as $validation) {
                if ($validation instanceof Validation\Required) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * If an element requires inline stylesheet definitions, this method is used send them to the browser before
     * the form is rendered.
     */
    public function renderCSS()
    {
    }

    /**
     * If an element requires javascript to be loaded, this method is used send them to the browser after
     * the form is rendered.
     */
    public function renderJS()
    {
    }

    /**
     * If an element requires inline stylesheet definitions, this method is used send them to the browser before
     * the form is rendered.
     */
    public function setClass($class)
    {
        $this->attributes['class'] = $class;
        if (!empty($this->attributes['class'])) {
            $this->attributes['class'] .= ' ' . $class;
        }
    }

    public function setForm(Form $form)
    {
        $this->form = $form;
    }

    public function setID($id)
    {
        $this->attributes['id'] = $id;
    }


    public function setValue($value)
    {
        $this->attributes['value'] = $value;
    }

    /**
     * This method provides a shortcut for applying the Required validation class to an element.
     */
    public function setRequired($required)
    {
        if (!empty($required)) {
            $this->validation[] = new Validation\Required;
        }
    }

    /**
     * This method applies one or more validation rules to an element.  If can accept a single concrete
     * validation class or an array of entries.
     *
     * @param array|string $validation
     */
    public function setValidation($validation)
    {
        /*If a single validation class is provided, an array is created in order to reuse the same logic.*/
        if (!is_array($validation)) {
            $validation = [$validation];
        }

        foreach ($validation as $object) {
            /*Ensures $object contains a existing concrete validation class.*/
            if ($object instanceof Validation) {
                $this->validation[] = $object;
            }
        }
    }
}
