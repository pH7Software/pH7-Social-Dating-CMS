<?php
/**
 * @title          Search User Core Form
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form
 * @version        1.2
 */
namespace PH7;

use
PH7\Framework\Session\Session,
PH7\Framework\Geo\Ip\Geo,
PH7\Framework\Mvc\Router\UriRoute;

class SearchUserCoreForm
{

    /**
     * @return array The 'sex_user' and 'match_sex'
     */
    public static function getGenderValues()
    {
        $sSexUser = 'male';
        $sMatchSex = 'female';

        if(UserCore::auth())
        {
            $sSexUser = (new UserModel)->getSex((new Session)->get('member_id'));
            $sMatchSex = ($sSexUser == 'male' ? 'female' : ($sSexUser == 'couple' ? 'couple' : 'male'));
        }

        return ['sex_user' => $sSexUser, 'match_sex' => $sMatchSex];
    }

    public static function quick($iWidth = 500)
    {
         // Generate the Quick Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => UriRoute::get('user','browse','index') . '/', 'method'=>'get'));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', array('male'=>t('Male'), 'female'=>t('Woman'), 'couple'=>t('Couple')), array('value'=>static::getGenderValues()['sex_user'], 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'sex', array('female'=>t('Woman'), 'male'=>t('Male'), 'couple'=>t('Couple')), array('value'=>array('male','female','couple'), 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Age);
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', array('id'=>'str_country', 'value'=>Geo::getCountryCode())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', array('id'=>'str_city')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'latest', array('1'=>'<span class="bold">' . t('Latest members') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'online', array('1'=>'<span class="bold green2">' . t('Only Online') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

    public static function advanced($iWidth = 500)
    {
         // Generate the Advanced Search form
        $oForm = new \PFBC\Form('form_search', $iWidth);
        $oForm->configure(array('action' => UriRoute::get('user','browse','index') . '/', 'method'=>'get' ));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_search', 'form_search'));
        $oForm->addElement(new \PFBC\Element\Select(t('I am a:'), 'match_sex', array('male'=>t('Male'), 'female'=>t('Woman'), 'couple'=>t('Couple')), array('value'=>static::getGenderValues()['sex_user'], 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Looking for:'), 'sex', array('female'=>t('Woman'), 'male'=>t('Male'), 'couple'=>t('Couple')), array('value'=>static::getGenderValues()['match_sex'], 'required'=>1)));
        $oForm->addElement(new \PFBC\Element\Age);
        $oForm->addElement(new \PFBC\Element\Country(t('Country:'), 'country', array('id'=>'str_country', 'value'=>Geo::getCountryCode())));
        $oForm->addElement(new \PFBC\Element\Textbox(t('City:'), 'city', array('id'=>'str_city')));
        $oForm->addElement(new \PFBC\Element\Textbox(t('State or Province:'), 'state', array('id'=>'str_state')));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Postal code (zip):'), 'zip_code', array('id'=>'str_zip_code')));
        $oForm->addElement(new \PFBC\Element\Email(t('Email Address:'), 'mail'));
        $oForm->addElement(new \PFBC\Element\Checkbox('', 'online', array('1'=>'<span class="bold green2">' . t('Only Online') . '</span>')));
        $oForm->addElement(new \PFBC\Element\Select(t('Browse By:'), 'order', array('latest'=>t('Latest Members'), 'last_activity'=>t('Last Activity'),'views'=>t('Most Popular'), 'rating'=>t('Top Rated'), 'username'=>t('Username'), 'first_name'=>t('First Name'), 'last_name'=>t('Last Name'), 'mail'=>t('Email'))));
        $oForm->addElement(new \PFBC\Element\Select(t('Direction:'), 'sort', array('desc'=>t('Descending'), 'asc'=>t('Ascending'))));
        $oForm->addElement(new \PFBC\Element\Button(t('Search'),'submit',array('icon'=>'search')));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="'.PH7_URL_STATIC.PH7_JS.'geo/autocompleteCity.js"></script>'));
        $oForm->render();
    }

}
