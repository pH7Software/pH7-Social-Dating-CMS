<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Navigation\Page;

class CountryController extends Controller
{
    const MAX_PROFILE_PER_PAGE = 20;

    public function index()
    {
        // Add Stylesheet tooltip
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tooltip.css');

        if ($this->httpRequest->getExists('country')) {
            // Get the country and city, limited to 50 characters and remove hyphens in too automatically insert the url.
            $this->registry->country = str_replace('-', ' ', substr($this->str->upperFirst($this->httpRequest->get('country')), 0, 50));
            $this->registry->city = ($this->httpRequest->getExists('city')) ? str_replace('-', ' ', substr($this->str->upperFirst($this->httpRequest->get('city')), 0, 50)) : '';

            // Set parameters Google Map
            $oMap = new Map;
            $oMap->setKey(DbConfig::getSetting('googleApiKey'));
            $oMap->setCenter($this->registry->country . ' ' . $this->registry->city);
            $oMap->setSize('100%', '520px');
            $oMap->setDivId('country_map');
            $oMap->setZoom(12);
            $oMap->addMarkerByAddress($this->registry->country . ' ' . $this->registry->city, t('Meet new people here!'));
            $oMap->generate();
            $this->view->map = $oMap->getMap();
            unset($oMap);

            $sCountryCode = $this->getCountryCode();

            // For User Model
            $this->view->userDesignModel = new UserDesignCoreModel;
            $this->view->country_code = $sCountryCode;
            $this->view->city = $this->registry->city;

            // Pagination
            $oPage = new Page;
            $iTotalUsers = (new UserCoreModel)->getGeoProfiles($sCountryCode, $this->registry->city, true, null, null, null);
            $this->view->total_pages = $oPage->getTotalPages($iTotalUsers, self::MAX_PROFILE_PER_PAGE);
            $this->view->current_page = $oPage->getCurrentPage();
            $this->view->first_user = $oPage->getFirstItem();
            $this->view->nb_user_by_page = $oPage->getNbItemsPerPage();

            // SEO Meta
            $this->view->page_title = t('Free online dating in %0% %1%, meet people, find friends. Single men & women in %2% %3%', $this->registry->country, $this->registry->city, $this->registry->country, $this->registry->city);
            $this->view->meta_description = t('Free online dating in %0% with single women & men. Personals, meet people & find friends in %1% on internet dating site. Find sweet love or sex dating and flirt in %2%, %3% with %site_name%', $this->registry->country, $this->registry->country, $this->registry->country, $this->registry->city);
            $this->view->meta_keywords = t('meeting woman, meeting man, %0%, %1%, meet people, networking, friends, communicate, meet online, online community, clubs, announces meeting, free dating, dating, %2% dating, communication, matrimonial meeting, sharing photos, flirt, finding friends, classifieds, personals, online, social networking', $this->registry->country, $this->registry->city, $this->registry->country);
            $this->view->h1_title = t('Meet new people in %0% %1%', '<span class="pH1">'.$this->registry->country.'</span>', '<span class="pH1">'.$this->registry->city.'</span>');
            $sMemberTxt = nt('%n% member', '%n% members', $iTotalUsers);
            $this->view->h3_title = t('%0% lives near %1% %2%', $sMemberTxt, $this->registry->country, $this->registry->city);

        } else {
            // Not found page
            Framework\Http\Http::setHeadersByCode(404);
            $this->view->error = t('Error, country is empty.');
        }

        $this->output();
    }

    private function getCountryCode()
    {
        $sCountryCode = Framework\CArray\CArray::getKeyByValIgnoreCase($this->registry->country, $this->registry->lang);
        return (strlen($sCountryCode) == 2) ? $sCountryCode : substr($this->registry->country, 0, 2);
    }
}
