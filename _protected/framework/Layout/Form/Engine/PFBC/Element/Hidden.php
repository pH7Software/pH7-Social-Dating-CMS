<?php
/**
 * We made many changes in this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Hidden extends \PFBC\Element
{

    protected $attributes = array('type' => 'hidden');

    public function __construct($sName, $sValue = '', array $aProperties = null)
    {
        if(!is_array($aProperties))
            $aProperties = array();

        if(isset($sValue))
            $aProperties['value'] = $sValue;

        // We remove the session of the hidden fields
        unset($_SESSION['pfbc'][\PFBC\Form::getFormId()]['values'][$sName]);

        parent::__construct('', $sName, $aProperties);
    }

}
