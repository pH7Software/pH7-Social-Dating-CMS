<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\CArray\CArray;
use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Geo\Map\Map as GeoMap;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Navigation\Page;
use Teapot\StatusCode;

class CountryController extends Controller
{
    const MAP_ZOOM_LEVEL = 12;
    const MAP_WIDTH_SIZE = '100%';
    const MAP_HEIGHT_SIZE = '520px';

    const MAX_PROFILE_PER_PAGE = 20;

    /**
     * @param string|null $sCountry
     * @param string|null $sCity
     *
     * @throws Framework\Http\Exception
     */
    public function index($sCountry = null, $sCity = null)
    {
        if ($sCountry !== null) {
            $this->registry->country = $this->getCountry($sCountry);
            $this->registry->city = $sCity !== null ? $this->getCity($sCity) : '';

            $sCountryCode = $this->getCountryCode();

            // For User Model
            $this->view->userDesignModel = new UserDesignCoreModel;
            $this->view->country_code = $sCountryCode;
            $this->view->city = $this->registry->city;

            // Pagination
            $oPage = new Page;
            $iTotalUsers = (new UserCoreModel)->getGeoProfiles(
                $sCountryCode,
                $this->registry->city,
                true,
                null,
                null,
                null
            );
            $this->view->total_pages = $oPage->getTotalPages($iTotalUsers, self::MAX_PROFILE_PER_PAGE);
            $this->view->current_page = $oPage->getCurrentPage();
            $this->view->first_user = $oPage->getFirstItem();
            $this->view->nb_user_by_page = $oPage->getNbItemsPerPage();

            $this->addTooltipCssFile();
            $this->setMap();
            $this->setMetaTags($iTotalUsers);
        } else {
            // Not found page
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
            $this->view->error = t('Oops! The country name is not specified.');
        }

        $this->output();
    }

    /**
     * @return string
     */
    private function getCountryCode()
    {
        $sCountryCode = CArray::getKeyByValueIgnoreCase($this->registry->country, $this->registry->lang);

        if (Map::isCountryCodeTooLong($sCountryCode)) {
            return substr(
                $this->registry->country,
                0,
                Map::COUNTRY_CODE_LENGTH
            );
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
        $sMemberTxt = nt('%n% member lives', '%n% members live', $iTotalUsers);
        $this->view->h3_title = t('%0% near %1% %2%', $sMemberTxt, $this->registry->country, $this->registry->city);
    }

    /**
     * Set the map to the view.
     *
     * @return void
     */
    private function setMap()
    {
        $sFullAddress = $this->registry->country . ' ' . $this->registry->city;
        $sMarkerText = t('Meet new people here thanks to <b>%site_name%</b>!');

        try {
            $oMapDrawer = new MapDrawerCore(
                new GeoMap,
                DbConfig::getSetting('googleApiKey')
            );
            $oMapDrawer->setWidthSize(self::MAP_WIDTH_SIZE);
            $oMapDrawer->setHeightSize(self::MAP_HEIGHT_SIZE);
            $oMapDrawer->setZoomLevel(self::MAP_ZOOM_LEVEL);
            $oMapDrawer->setDivId('country_map');
            $sContent = $oMapDrawer->getMap($sFullAddress, $sMarkerText);
        } catch (PH7InvalidArgumentException $oE) {
            $sContent = sprintf('<strong>%s</strong>', $oE->getMessage());
        }

        $this->view->map = $sContent;
    }

    /**
     * Gives the country name limited to 50 chars and removes dashes automatically added from the URL.
     *
     * @param string $sCountry
     *
     * @return string
     */
    private function getCountry($sCountry)
    {
        return str_replace(
            '-',
            ' ',
            substr($this->str->upperFirst($sCountry), 0, Map::MAX_COUNTRY_LENGTH)
        );
    }

    /**
     * Gives the city name limited to 50 chars and removes dashes automatically added from the URL.
     *
     * @param string $sCity
     *
     * @return string
     */
    private function getCity($sCity)
    {
        return str_replace(
            '-',
            ' ',
            substr($this->str->upperFirst($sCity), 0, Map::MAX_CITY_LENGTH)
        );
    }

    /**
     * Add the stylesheet for the tooltips on the page.
     *
     * @return void
     */
    private function addTooltipCssFile()
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'tooltip.css'
        );
    }
}
