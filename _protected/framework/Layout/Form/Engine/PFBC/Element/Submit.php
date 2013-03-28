<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */
namespace PFBC\Element;

class Submit extends \PFBC\Element
{

    public function __construct($sLabel, array $aProperties = null)
    {
        $this->attributes = array('type' => 'submit', 'value' => $sLabel);

        if(!is_array($aProperties))
            $aProperties = array();

        parent::__construct('', '', $aProperties);
    }

}
