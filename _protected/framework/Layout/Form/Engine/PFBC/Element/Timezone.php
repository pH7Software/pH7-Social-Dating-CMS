<?php
/**
 * File created by Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

class Timezone extends Select
{
    const TIMEZONES = [
        '-12',
        '-11',
        '-10',
        '-9',
        '-8',
        '-7',
        '-6',
        '-5',
        '-4',
        '-3.5',
        '-3',
        '-2',
        '-1',
        '+0',
        '+1',
        '+2',
        '+3',
        '+3.5',
        '+4',
        '+4.5',
        '+5',
        '+5.5',
        '+6',
        '+7',
        '+8',
        '+8.75',
        '+9',
        '+9.5',
        '+10',
        '+10.5',
        '+11',
        '+11.5',
        '+12',
        '+12.75',
        '+13',
        '+14'
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = [];

        foreach (self::TIMEZONES as $sValue) {
            $aOptions[$sValue] = t($sValue);
        }

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }
}
