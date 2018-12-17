<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Layout / Form / Engine / PFBC / Element
 */

namespace PFBC\Element;

use PFBC\OptionElement;
use PH7\Form;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\DbConfig;

class Price extends OptionElement
{
    const MIN_PRICE_TYPE = 'min_price';
    const MAX_PRICE_TYPE = 'max_price';

    const CACHE_GROUP = 'str/form/pfbc';
    const CACHE_LIFETIME = 31536000;

    const MIN_PRICE = 500;
    const MAX_PRICE = 100000;
    const RANGE_NUMBER_INTERVAL = 500;

    /** @var string */
    private $sHtmlOutput;

    /** @var int */
    private $iMinPrice;

    /** @var int */
    private $iMaxPrice;

    /**
     * Generate the select field for price search.
     *
     * @param array|null $aProperties
     */
    public function __construct($aProperties = null)
    {
        parent::__construct('', '', [], $aProperties);

        $this->iMinPrice = self::MIN_PRICE;
        $this->iMaxPrice = self::MAX_PRICE;

        $sSelect1 = static::getOptions(static::MIN_PRICE_TYPE);
        $sSelect2 = static::getOptions(static::MAX_PRICE_TYPE);

        $this->sHtmlOutput = '<div class="pfbc-label"><label>' . t('Price Range') . '</label></div><select name="minPrice">' . $sSelect1 . '</select> - <select name="maxPrice">' . $sSelect2 . '</select>';
    }

    public function render()
    {
        echo $this->sHtmlOutput;
    }

    /**
     * @param string $sType 'min_price' or 'max_price'
     *
     * @return string The price field with the default selected minimum and maximum price range.
     */
    private function getOptions($sType)
    {
        $oCache = (new Cache)->start(
            self::CACHE_GROUP,
            $sType,
            self::CACHE_LIFETIME
        );

        if (!$sSelect = $oCache->get()) {
            $sSelect = '';
            $sAttrName = $sType === static::MIN_PRICE_TYPE ? 'iMinPrice' : 'iMaxPrice';

            for ($iPrice = $this->iMinPrice; $iPrice <= $this->iMaxPrice; $iPrice += self::RANGE_NUMBER_INTERVAL) {
                $sSelect .= '<option value="' . $iPrice . '"';

                if (
                    !empty($this->attributes['value'][$sType]) && $iPrice === $this->attributes['value'][$sType] ||
                    $iPrice === $this->$sAttrName
                ) {
                    $sSelect .= ' selected="selected"';
                }

                $sSelect .= '>' . number_format($iPrice) . '</option>';
            }

            $oCache->put($sSelect);
        }
        unset($oCache);

        return $sSelect;
    }
}
