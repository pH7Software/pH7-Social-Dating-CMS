<?php
/**
 * @title          Search User Core Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 * @version        1.5
 */
namespace PH7;

use
PH7\Framework\Math\Measure\Year,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Router\Uri;

class SearchUserCoreForm
{
    /**
     * Defaut field attriutes.
     */
    private static $aSexOption = ['required' => 1];
    private static $aMatchSexOption = ['required' => 1];
    private static $aAgeOption = null;
    private static $aCountryOption = ['id' => 'str_country'];
    private static $aCityOption = ['id' => 'str_city'];
    private static $aStateOption = ['id' => 'str_state'];

    /**
     * @param integer $iWidth Width of the form in pixel. Default: NULL (so will be 100%)
     * @param boolean $bSetDevVals Set default values in the form fields, or not... Default: TRUE
     * @return void HTML output.
     */
    public static function quick($iWidth = null, $bSetDevVals = true)
    {
        if ($bSetDevVals) {
            static::setAttrVals($bSetDevVals);
        }

         // Generate the Quick Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => Uri::get('user','browse','index') . PH7_SH, 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', ['male' => t('Man'), 'female' => t('Woman'), 'couple' => t('Couple')], self::$aSexOption));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for a:'), 'sex', ['female' => t('Woman'), 'male' => t('Man'), 'couple' => t('Couple')], self::$aMatchSexOption));
        $oForm->addElement(new \PFBC\Element\Age(self::$aAgeOption));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', self::$aCountryOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', self::$aCityOption));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'latest', array('1' => '<span class="bold">' . t('Latest members') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'avatar', array('1' => '<span class="bold">' . t('Only with Avatar') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'online', array('1' => '<span class="bold green2">' . t('Only Online') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit', array('icon' => 'search')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    /**
     * @param integer $iWidth Width of the form in pixel. Default: NULL (so will be 100%)
     * @param boolean $bSetDevVals Set default values in the form fields, or not... Default: TRUE
     * @return void HTML output.
     */
    public static function advanced($iWidth = null, $bSetDevVals = true)
    {
        if ($bSetDevVals) {
            static::setAttrVals($bSetDevVals);
        }

         // Generate the Advanced Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => Uri::get('user','browse','index') . PH7_SH, 'method' => 'get' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', array('male' => t('Male'), 'female' => t('Woman'), 'couple' => t('Couple')), self::$aSexOption));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'sex', array('female' => t('Woman'), 'male' => t('Male'), 'couple' => t('Couple')), self::$aMatchSexOption));
        $oForm->addElement(new \PFBC\Element\Age(self::$aAgeOption));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', self::$aCountryOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', self::$aCityOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State/Province:'), 'state', self::$aStateOption));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal Code:'), 'zip_code', array('id' => 'str_zip_code')));
        $oForm->addElement(new \PFBC\Element\Email(t('Email Address:'), 'mail'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'avatar', array('1' => '<span class="bold">' . t('Only with Avatar') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'online', array('1' => '<span class="bold green2">' . t('Only Online') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array(SearchCoreModel::LATEST => t('Latest Members'), SearchCoreModel::LAST_ACTIVITY => t('Last Activity'), SearchCoreModel::VIEWS => t('Most Popular'), SearchCoreModel::RATING => t('Top Rated'), SearchCoreModel::USERNAME => t('Username'), SearchCoreModel::FIRST_NAME => t('First Name'), SearchCoreModel::LAST_NAME => t('Last Name'), SearchCoreModel::EMAIL => t('Email'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array(SearchCoreModel::DESC => t('Descending'), SearchCoreModel::ASC => t('Ascending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit', array('icon' => 'search')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    /**
     * If a user is logged, get the relative 'user_sex' and 'match_sex' for better and more intuitive search.
     *
     * @param object \PH7\UserCoreModel $oUserModel
     * @param object \PH7\Framework\Session\Session $oSession
     * @return array The 'user_sex' and 'match_sex'
     */
    protected static function getGenderVals(UserCoreModel $oUserModel, Session $oSession)
    {
        $sUserSex = 'male';
        $aMatchSex = ['male', 'female', 'couple'];

        if(UserCore::auth())
        {
            $sUserSex = $oUserModel->getSex($oSession->get('member_id'));
            $aMatchSex = Form::getVal($oUserModel->getMatchSex($oSession->get('member_id')));
        }

        return ['user_sex' => $sUserSex, 'match_sex' => $aMatchSex];
    }

    /**
     * If a user is logged, get "approximately" the relative age for better and more intuitive search.
     *
     * @param object \PH7\UserCoreModel $oUserModel
     * @param object \PH7\Framework\Session\Session $oSession
     * @return array 'min_age' and 'max_age' which is the approximately age the user is looking for.
     */
    protected static function getAgeVals(UserCoreModel $oUserModel, Session $oSession)
    {
        $iMinAge = (int) DbConfig::getSetting('minAgeRegistration');
        $iMaxAge = (int) DbConfig::getSetting('maxAgeRegistration');

        if(UserCore::auth())
        {
            $sBirthDate = $oUserModel->getBirthDate($oSession->get('member_id'));
            $aAge = explode('-', $sBirthDate);
            $iAge = (new Year($aAge[0], $aAge[1], $aAge[2]))->get();
            $iMinAge = ($iAge-5 < $iMinAge) ? $iMinAge : $iAge-5;
            $iMaxAge = ($iAge+5 > $iMaxAge) ? $iMaxAge : $iAge+5;
        }

        return ['min_age' => $iMinAge, 'max_age' => $iMaxAge];
    }

    /**
     * Set the default values for the fields in search forms.
     *
     * @param return void
     */
    protected static function setAttrVals()
    {
        $oHttpRequest = new Http;
        $oSession = new Session;
        $oUserModel = new UserCoreModel;

        if ($oHttpRequest->getExists('match_sex')) {
            self::$aSexOption += ['value' => $oHttpRequest->get('match_sex')];
        } else {
            self::$aSexOption += ['value' => static::getGenderVals($oUserModel, $oSession)['user_sex']];
        }

        if ($oHttpRequest->getExists('sex')) {
            self::$aMatchSexOption += ['value' => $oHttpRequest->get('sex')];
        } else {
            self::$aMatchSexOption += ['value' => static::getGenderVals($oUserModel, $oSession)['match_sex']];
        }

        if ($oHttpRequest->getExists(['age1', 'age2'])) {
            self::$aAgeOption = ['value' => ['min_age' => $oHttpRequest->get('age1'), 'max_age' => $oHttpRequest->get('age2')]];
        } else {
            self::$aAgeOption = ['value' => static::getAgeVals($oUserModel, $oSession)];
        }

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

        self::$aStateOption += ['value'=> Geo::getState(), 'onfocus' => "if('" . Geo::getState() . "' == this.value) this.value = '';", 'onblur' => "if ('' == this.value) this.value = '" . Geo::getState() . "';"];
    }
}
