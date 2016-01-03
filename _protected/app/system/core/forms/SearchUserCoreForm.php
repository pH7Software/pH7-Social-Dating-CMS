<?php
/**
 * @title          Search User Core Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 * @version        1.2
 */
namespace PH7;

use
PH7\Framework\Math\Measure\Year,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Session\Session,
PH7\Framework\Mvc\Router\Uri;

class SearchUserCoreForm
{

    public static function quick($iWidth = 500)
    {
        $oSession = new Session;
        $oUserModel = new UserCoreModel;

         // Generate the Quick Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => Uri::get('user','browse','index') . PH7_SH, 'method' => 'get'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', array('male' => t('Male'), 'female' => t('Woman'), 'couple' => t('Couple')), array('value' => static::getGenderVals($oUserModel, $oSession)['user_sex'], 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'sex', array('female' => t('Woman'), 'male' => t('Male'), 'couple' => t('Couple')), array('value' => static::getGenderVals($oUserModel, $oSession)['match_sex'], 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Age(array('value' => static::getAgeVals($oUserModel, $oSession))));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', array('id' => 'str_country', 'value' => Geo::getCountryCode())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', array('id'=>'str_city', 'value'=> Geo::getCity())));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'latest', array('1' => '<span class="bold">' . t('Latest members') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'avatar', array('1' => '<span class="bold">' . t('Only with Avatar') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'online', array('1' => '<span class="bold green2">' . t('Only Online') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit', array('icon' => 'search')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    public static function advanced($iWidth = 500)
    {
        $oSession = new Session;
        $oUserModel = new UserCoreModel;

         // Generate the Advanced Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => Uri::get('user','browse','index') . PH7_SH, 'method' => 'get' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', array('male' => t('Male'), 'female' => t('Woman'), 'couple' => t('Couple')), array('value' => static::getGenderVals($oUserModel, $oSession)['user_sex'], 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'sex', array('female' => t('Woman'), 'male' => t('Male'), 'couple' => t('Couple')), array('value' => static::getGenderVals($oUserModel, $oSession)['match_sex'], 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Age(array('value' => static::getAgeVals($oUserModel, $oSession))));
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', array('id' => 'str_country', 'value' => Geo::getCountryCode())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', array('id'=>'str_city', 'value'=> Geo::getCity())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State or Province:'), 'state', array('id' => 'str_state', 'value'=> Geo::getState())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('ZIP/Postal Code:'), 'zip_code', array('id' => 'str_zip_code')));
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

}
