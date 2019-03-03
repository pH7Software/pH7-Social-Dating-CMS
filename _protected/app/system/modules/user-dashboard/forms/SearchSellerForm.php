<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form
 */

namespace PH7;

use PH7\Framework\Geo\Ip\Geo;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;

class SearchSellerForm
{
    /**
     * Default field attributes.
     */
    private static $aCountryOption = ['id' => 'str_country'];
    private static $aCityOption = ['id' => 'str_city'];
    private static $aStateOption = ['id' => 'str_state'];
    private static $aLatestOrder = [];
    private static $aOnlineOnly = [];

    public static function display($iWidth = null, $bSetDefVals = true)
    {
        if ($bSetDefVals) {
            self::setAttrVals();
        }

        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(['action' => Uri::get('realestate', 'browse', 'seller') . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new \PFBC\Element\Hidden('sex', 'seller'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Hidden('sex', 'seller'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City 1:'), 'city', self::$aCityOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City 2:'), 'city2'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City 3:'), 'city3'));
        $oForm->addElement(new \PFBC\Element\Price);
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Min Bedrooms:'),
                SearchQueryCore::BEDROOM,
                [0, 1, 2, 3, 4, 5, 6],
                [
                    'value' => 0,
                    'min' => 0
                ]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Min Bathrooms:'),
                SearchQueryCore::BATHROOM,
                [0, 1, 2, 3, 4],
                [
                    'value' => 0,
                    'min' => 0
                ]
            )
        );
        $oForm->addElement(new \PFBC\Element\Number(t('Size:'), SearchQueryCore::SIZE, ['value' => 0, 'min' => 0]));
        $oForm->addElement(
            new \PFBC\Element\Number(
                t('Min Year Built:'),
                SearchQueryCore::YEAR_BUILT,
                [
                    'value' => date('Y') - 20,
                    'min' => 0,
                    'max' => date('Y')
                ]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Home Type:'),
                SearchQueryCore::HOME_TYPE,
                [
                    'family' => t('Single Family'),
                    'condo' => t('Condo/Townhouse')
                ]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Home Style:'),
                SearchQueryCore::HOME_STYLE,
                [
                    'rambler' => t('Rambler'),
                    'ranch' => t('Ranch/Patio'),
                    'tri-multi-level' => t('Tri-Multi-Level'),
                    'two-story' => t('Two Story'),
                    'any' => t('Any')
                ]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Number(
                t('Min Square Feet:'),
                SearchQueryCore::HOME_SQUARE_FT,
                ['value' => 0, 'min' => 0]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Number(
                t('Min Lot Size:'),
                SearchQueryCore::HOME_LOT_SIZE,
                ['value' => 0, 'min' => 0]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Min Garage Spaces:'),
                SearchQueryCore::HOME_GARAGE_SPACE,
                [0, 1, 2, 3, 4],
                ['value' => 0, 'min' => 0]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Select(
                t('Min Carport Spaces:'),
                SearchQueryCore::HOME_CARPORT_SPACE,
                [0, 1, 2],
                ['value' => 0, 'min' => 0]
            )
        );
        $oForm->addElement(
            new \PFBC\Element\Date(
                t('Only show listings from this date:'),
                SearchQueryCore::FROM_DATE
            )
        );
        //$oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', [SearchCoreModel::LATEST => t('Latest Members'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::VIEWS => t('Most Popular'), SearchCoreModel::RATING => t('Top Rated'), SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Search Direction:'), 'sort', [SearchCoreModel::DESC => t('Descending'), SearchCoreModel::ASC => t('Ascending')]));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'), 'submit', ['icon' => 'search']));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    /**
     * Set the default values for the fields in search forms.
     *
     * @return void
     */
    protected static function setAttrVals()
    {
        $oHttpRequest = new HttpRequest;

        if ($oHttpRequest->getExists('country')) {
            self::$aCountryOption += ['value' => $oHttpRequest->get('country')];
        } else {
            self::$aCountryOption += ['value' => Geo::getCountryCode()];
        }

        if ($oHttpRequest->getExists('city')) {
            $sCity = $oHttpRequest->get('city');
        } else {
            $sCity = Geo::getCity();
        }
        self::$aCityOption += ['value' => $sCity, 'onfocus' => "if('" . $sCity . "' == this.value) this.value = '';", 'onblur' => "if ('' == this.value) this.value = '" . $sCity . "';"];

        self::$aStateOption += ['value' => Geo::getState(), 'onfocus' => "if('" . Geo::getState() . "' == this.value) this.value = '';", 'onblur' => "if ('' == this.value) this.value = '" . Geo::getState() . "';"];

        if ($oHttpRequest->getExists('latest')) {
            self::$aLatestOrder += ['value' => '1'];
        }

        if ($oHttpRequest->getExists('online')) {
            self::$aOnlineOnly += ['value' => '1'];
        }
    }
}
