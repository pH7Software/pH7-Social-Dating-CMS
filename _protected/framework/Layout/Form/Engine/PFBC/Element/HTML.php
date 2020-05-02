<?php

namespace PFBC\Element;

use PFBC\Element;

class HTML extends Element
{
    public function __construct($value)
    {
        $properties = ['value' => $value];
        parent::__construct('', '', $properties);
    }

    public function render()
    {
        echo $this->attributes['value'];
    }
}
