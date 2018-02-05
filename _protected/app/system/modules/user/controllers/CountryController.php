<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\CArray\CArray;
use PH7\Framework\Geo\Map\Map;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Navigation\Page;

class CountryController extends Controller
{
    const MAP_ZOOM_LEVEL = 12;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '520px';

    const COUNTRY_CODE_LENGTH = 2;
    const MAX_PROFILE_PER_PAGE = 20;
    const MAX_COUNTRY_LENGTH = 50;
    const MAX_CITY_LENGTH = 50;

    public function index()
    {
        // Add Stylesheet tooltip
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tooltip.css');

        if ($this->httpRequest->getExists('country')) {
            // Get the country and city, limited to 50 characters and remove dashes automatically added from the URL
            $this->registry->country = $this->getCountry();
            $this->registry->city = $this->httpRequest->getExists('city') ? $this->getCity() : '';

            $this->setMap();

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
            $this->setMetaTags($iTotalUsers);
        } else {
            // Not found page
            Http::setHeadersByCode(self::HTTP_NOT_FOUND_CODE);
            $this->view->error = t('Error, country is empty.');
        }

        $this->output();
    }

    /**
     * @return string
     */
    private function getCountryCode()
    {
        $sCountryCode = CArray::getKeyByValIgnoreCase($this->registry->country, $this->registry->lang);

        if (strlen($sCountryCode) !== self::COUNTRY_CODE_LENGTH) {
            return substr($this->registry->country, 0, self::COUNTRY_CODE_LENGTH);
        }

        return $sCountryCode;
    }

    /**
     * Assign SEO meta tags to the template.
     *
     * @param int $iTotalUsers
     *
     * @return void
     */
    private function setMetaTags($iTotalUsers)
    {
        $this->view->page_title = t('Free online dating in %0% %1%, meet people, find friends. Single men & women in %2% %3%', $this->registry->country, $this->registry->city, $this->registry->country, $this->registry->city);
        $this->view->meta_description = t('Free online dating in %0% with single women & men. Personals, meet people & find friends in %1% on internet dating site. Find sweet love or sex dating and flirt in %2%, %3% with %site_name%', $this->registry->country, $this->registry->country, $this->registry->country, $this->registry->city);
        $this->view->meta_keywords = t('meeting woman, meeting man, %0%, %1%, meet people, networking, friends, communicate, meet online, online community, clubs, announces meeting, free dating, dating, %2% dating, communication, matrimonial meeting, sharing photos, flirt, finding friends, classifieds, personals, online, social networking', $this->registry->country, $this->registry->city, $this->registry->country);
        $this->view->h1_title = t('Meet new people in %0% %1%', '<span class="pH1">' . $this->registry->country . '</span>', '<span class="pH1">' . $this->registry->city . '</span>');
        $sMemberTxt = nt('%n% member', '%n% members', $iTotalUsers);
        $this->view->h3_title = t('%0% lives near %1% %2%', $sMemberTxt, $this->registry->country, $this->registry->city);
    }

    /**
     * Set the map to the view.
     *
     * @return void
     */
    private function setMap()
    {
        $sFullAddress = $this->registry->country . ' ' . $this->registry->city;
        $sMarkerText = t('Meet new people here thanks <b>%site_name%</b>!');

        $oMap = new Map;
        $oMap->setKey(DbConfig::getSetting('googleApiKey'));
        $oMap->setCenter($sFullAddress);
        $oMap->setSize(self::MAP_WIDTH_SIZE, self::MAP_HEIGHT_SIZE);
        $oMap->setDivId('country_map');
        $oMap->setZoom(self::MAP_ZOOM_LEVEL);
        $oMap->addMarkerByAddress($sFullAddress, $sMarkerText, $sMarkerText);
        $oMap->generate();
        $this->view->map = $oMap->getMap();
        unset($oMap);
    }

    /**
     * @return string
     */
    private function getCountry()
    {
        return str_replace(
            '-',
            ' ',
            substr($this->str->upperFirst($this->httpRequest->get('country')), 0, self::MAX_COUNTRY_LENGTH)
        );
    }

    /**
     * @return string
     */
    private function getCity()
    {
        return str_replace(
            '-',
            ' ',
            substr($this->str->upperFirst($this->httpRequest->get('city')), 0, self::MAX_CITY_LENGTH)
        );
    }
}
