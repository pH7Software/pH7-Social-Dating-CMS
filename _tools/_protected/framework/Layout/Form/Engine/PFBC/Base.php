<?php
/**
 * Many changes have been made in this file.
 * By Pierre-Henry Soria <https://ph7.me>
 */

namespace PFBC;

abstract class Base
{
    public function configure(array $properties = null)
    {
        if (!empty($properties)) {
            $class = get_class($this);

            /*The property_reference lookup array is created so that properties can be set
            case-insensitively.*/
            $available = array_keys(get_class_vars($class));
            $property_reference = [];
            foreach ($available as $property) {
                $property_reference[strtolower($property)] = $property;
            }

            /*The method reference lookup array is created so that "set" methods can be called
            case-insensitively.*/
            $available = get_class_methods($class);
            $method_reference = [];
            foreach ($available as $method) {
                $method_reference[strtolower($method)] = $method;
            }

            foreach ($properties as $property => $value) {
                $property = strtolower($property);
                /*The attributes property cannot be set directly.*/
                if ($property !== 'attributes') {
                    /*If the appropriate class has a "set" method for the property provided, then
                    it is called instead or setting the property directly.*/
                    if (isset($method_reference['set' . $property])) {
                        $this->{$method_reference['set' . $property]}($value);
                    } elseif (isset($property_reference[$property])) {
                        $this->{$property_reference[$property]} = $value;
                    } /*Entries that don't match an available class property are stored in the attributes
                    property if applicable.  Typically, these entries will be element attributes such as
                    class, value, onkeyup, etc.*/
                    elseif (isset($property_reference['attributes'])) {
                        $this->attributes[$property] = $value;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * This method can be used to view a class' state.
     */
    public function debug()
    {
        echo '<pre>', print_r($this, true), '</pre>';
    }

    /**
     * This method is used by the Form class and all Element classes to return a string of html
     * attributes.
     * There is an ignore parameter that allows special attributes from being included.
     *
     * @param array|string $ignore
     *
     * @return string
     */
    public function getAttributes($ignore = '')
    {
        $str = '';
        if (!empty($this->attributes)) {
            if (!is_array($ignore)) {
                $ignore = [$ignore];
            }

            $attributes = array_diff(array_keys($this->attributes), $ignore);
            foreach ($attributes as $attribute) {
                $str .= ' ' . $attribute . '="' . $this->filter($this->attributes[$attribute]) . '"';
            }
        }
        return $str;
    }

    /**
     * This method converted special characters to entities in HTML attributes from breaking the markup.
     *
     * @param string $sText
     *
     * @return string
     */
    protected function filter($sText)
    {
        return htmlspecialchars($sText, ENT_QUOTES);
    }
}
