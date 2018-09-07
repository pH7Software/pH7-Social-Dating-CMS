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
        'AUD' => '(AUD) Australian Dollar',
        'BRL' => '(BRL) Brazilian Real',
        'CAD' => '(CAD) Canadian Dollar',
        'CZK' => '(CZK) Czech Koruna',
        'DKK' => '(DKK) Danish Krone',
        'EUR' => '(EUR) Euro',
        'HKD' => '(HKD) Hong Kong Dollar',
        'HUF' => '(HUF) Hungarian Forint',
        'ILS' => '(ILS) Israeli New Sheqel',
        'JPY' => '(JPY) Japanese Yen',
        'MYR' => '(MYR) Malaysian Ringgit',
        'MXN' => '(MXN) Mexican Peso',
        'NOK' => '(NOK) Norwegian Krone',
        'NZD' => '(NZD) New Zealand Dollar',
        'PHP' => '(PHP) Philippine Peso',
        'PLN' => '(PLN) Polish Zloty',
        'GBP' => '(GBP) Pound Sterling',
        'SGD' => '(SGD) Singapore Dollar',
        'SEK' => '(SEK) Swedish Krona',
        'CHF' => '(CHF) Swiss Franc',
        'TWD' => '(TWD) Taiwan New Dollar',
        'THB' => '(THB) Thai Baht',
        'TRY' => '(TRY) Turkish Lira',
        'USD' => '(USD) U.S. Dollar'
    ];

    /**
     * @param string $sLabel
     * @param string $sName
     * @param array|null $aProperties
     */
    public function __construct($sLabel, $sName, array $aProperties = null)
    {
        $aOptions = [];

        foreach (static::CURRENCY_CODES as $sCurrencyCode => $sCurrencyName) {
            $aOptions[$sCurrencyCode] = t($sCurrencyName);
        }

        parent::__construct($sLabel, $sName, $aOptions, $aProperties);
    }
}