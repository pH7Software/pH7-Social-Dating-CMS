<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

use PFBC\Element;
use PFBC\Form;

class Hidden extends Element
{
    /** @var array */
    protected $attributes = ['type' => 'hidden'];

    public function __construct($sName, $sValue = '', array $aProperties = null)
    {
        if (!is_array($aProperties))
            $aProperties = [];

        if (isset($sValue)) {
            $aProperties['value'] = $sValue;
        }

        // We remove the session of the hidden fields
        unset($_SESSION['pfbc'][Form::getFormId()]['values'][$sName]);

        parent::__construct('', $sName, $aProperties);
    }
}
