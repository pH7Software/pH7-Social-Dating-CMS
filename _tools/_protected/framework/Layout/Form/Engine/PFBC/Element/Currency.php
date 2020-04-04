<?php
/**
 * @author Pierre-Henry Soria <hi@ph7.me>
 */

namespace PFBC\Element;

class Currency extends Select
{
    /**
     * @var array The three-character currency codes
     * https://en.wikipedia.org/wiki/ISO_4217#Active_codes
     */
    const CURRENCY_CODES = [
        'AUD',
        'BRL',
        'CAD',
        'CHF',
        'CLP',
        'CZK',
        'DKK',
        'EUR',
        'GBP',
        'HKD',
        'HUF',
        'ILS',
        'INR',
        'JPY',
        'MXN',
        'MYR',
        'NOK',
        'NZD',
        'PHP',
        'PLN',
        'RUB',
        'SEK',
        'SGD',
        'THB',
        'TRY',
        'TWD',
        'USD',
        'ZAR'
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = [];

        foreach (static::CURRENCY_CODES as $sCurrencyCode) {
            $aOptions[$sCurrencyCode] = t($sCurrencyCode);
        }

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }
}
