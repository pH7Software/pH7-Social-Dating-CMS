<?php
/**
 * We made this code.
 * By pH7 (Pierre-Henry SORIA).
 */

namespace PFBC\Element;

class Submit extends \PFBC\Element
{
    /**
     * @param string $sLabel
     * @param array|null $aProperties
     */
    public function __construct($sLabel, array $aProperties = null)
    {
        $this->attributes = ['type' => 'submit', 'value' => $sLabel];

        if (!is_array($aProperties)) {
            $aProperties = [];
        }

        parent::__construct('', '', $aProperties);
    }
}
