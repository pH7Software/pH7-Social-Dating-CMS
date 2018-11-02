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

class SearchBuyerForm
{
    /**
     * Default field attributes.
     */
    private static $aCountryOption = ['id' => 'str_country'];
    private static $aCityOption = ['id' => 'str_city'];
    private static $aStateOption = ['id' => 'str_state'];
    private static $aLatestOrder = [];
    private static $aAvatarOnly = [];
    private static $aOnlineOnly = [];

    public static function display($iWidth = null, $bSetDefVals = true)
    {
        if ($bSetDefVals) {
            self::setAttrVals();
        }

        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(['action' => Uri::get('realestate', 'browse', 'index') . PH7_SH, 'method' => 'get']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Hidden('sex', 'buyer'));
        $oForm->addElement(new \PFBC\Element\Hidden('match_sex', 'seller'));
        $oForm->addElement(new \PFBC\Element\Select(t('Country:'), 'country', Form::getCountryValues(), self::$aCountryOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', self::$aCityOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State/Province:'), 'state', self::$aStateOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), 'zip_code', ['id' => 'str_zip_code']));
        $oForm->addElement(new \PFBC\Element\Email(t('Email Address:'), 'mail'));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', [SearchCoreModel::LATEST => t('Latest Members'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::VIEWS => t('Most Popular'), SearchCoreModel::RATING => t('Top Rated'), SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email')]));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', [SearchCoreModel::DESC => t('Descending'), SearchCoreModel::ASC => t('Ascending')]));
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

        if ($oHttpRequest->getExists('avatar')) {
            self::$aAvatarOnly += ['value' => '1'];
        }

        if ($oHttpRequest->getExists('online')) {
            self::$aOnlineOnly += ['value' => '1'];
        }
    }
}
