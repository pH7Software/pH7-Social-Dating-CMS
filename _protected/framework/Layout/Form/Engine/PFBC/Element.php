<?php
/**
 * Many changes have been made in this file.
 * By Pierre-Henry SORIA.
 */

namespace PFBC;

abstract class Element extends Base
{
    protected $attributes;
    protected $form;
    protected $label;
    protected $description;
    protected $validation = array();
    protected $preHTML;
    protected $postHTML;
    protected $width;
    private $errors = array();

    public function __construct($label, $name, array $properties = null)
    {
        $configuration = array(
            'label' => $label,
            'name' => $name
        );

        /*Merge any properties provided with an associative array containing the label
        and name properties.*/
        if (is_array($properties)) {
            $configuration = array_merge($configuration, $properties);
        }

        $this->configure($configuration);
    }

    /*When an element is serialized and stored in the session, this method prevents any non-essential
    information from being included.*/
    public function __sleep()
    {
        return array('attributes', 'label', 'validation');
    }

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
        if (substr($width, -2) == 'px') {
            $width = substr($width, 0, -2);
        } elseif (substr($width, -1) == '%') {
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
                if (substr($element, -1) == ":") {
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

    public function jQueryDocumentReady()
    {
    }

    public function jQueryOptions()
    {
        if (!empty($this->jQueryOptions)) {
            $options = "";
            foreach ($this->jQueryOptions as $option => $value) {
                if (!empty($options))
                    $options .= ', ';
                $options .= $option . ': ';
                /*When javascript needs to be applied as a jQuery option's value, no quotes are needed.*/
                if (is_string($value) && substr($value, 0, 3) == 'js:')
                    $options .= substr($value, 3);
                else
                    $options .= var_export($value, true);
            }
            echo '{ ', $options, ' }';
        }
    }

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
        if ($this->isRequired() && static::class !== 'PFBC\Element\CKEditor') {
            $sCode .= ' required="required"';
        }

        return $sCode;
    }

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

    public function renderCSS()
    {
    }

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

    public function setValidation($validation)
    {
        /*If a single validation class is provided, an array is created in order to reuse the same logic.*/
        if (!is_array($validation)) {
            $validation = array($validation);
        }

        foreach ($validation as $object) {
            /*Ensures $object contains a existing concrete validation class.*/
            if ($object instanceof Validation) {
                $this->validation[] = $object;
            }
        }
    }
}
