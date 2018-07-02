<?php
/**
 * By Pierre-Henry SORIA <http://ph7.me>
 */

namespace PFBC\Element;

class YesNo extends Radio
{
    /** @var array */
    private static $aOptions = [
        '1' => 'Yes',
        '0' => 'No'
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        if (!is_array($aProperties)) {
            $aProperties = ['inline' => 1];
        } elseif (!array_key_exists('inline', $aProperties)) {
            $aProperties['inline'] = 1;
        }

        parent::__construct($sLabel, $sName, self::$aOptions, $aProperties);
    }
}
