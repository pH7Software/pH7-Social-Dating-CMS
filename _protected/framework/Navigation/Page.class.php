<?php
/**
 * @desc             Various Page methods with also the pagination methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Navigation
 */

namespace PH7\Framework\Navigation;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;

class Page
{
    const DEFAULT_NUMBER_ITEMS = 10;

    const REGEX_URL_PARAMS = '#\?(.+[^\./])=(.+[^\./])$#';
    const REGEX_URL_QUESTION_MARKS = '#\?.+$#';

    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var int */
    private $iTotalPages;

    /** @var int */
    private $iTotalItems;

    /** @var int */
    private $iNbItemsPerPage;

    /** @var int */
    private $iCurrentPage;

    /** @var int */
    private $iFirstItem;

    public function __construct()
    {
        $this->oHttpRequest = new HttpRequest;
    }

    /**
     * @param int $iTotalItems
     * @param int $iNbItemsPerPage Default 10
     *
     * @return int The number of pages.
     */
    public function getTotalPages($iTotalItems, $iNbItemsPerPage = self::DEFAULT_NUMBER_ITEMS)
    {
        $this->totalPages($iTotalItems, $iNbItemsPerPage);

        return ($this->iTotalPages < 1) ? 1 : $this->iTotalPages;
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->iTotalItems;
    }

    /**
     * @return int
     */
    public function getFirstItem()
    {
        return $this->iFirstItem < 0 ? 0 : $this->iFirstItem;
    }

    /**
     * @return int
     */
    public function getNbItemsPerPage()
    {
        return $this->iNbItemsPerPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->iCurrentPage;
    }

    /**
     * Clean a Dynamic URL for some features CMS.
     *
     * @param string $sVar The Query URL (e.g. www.pierre-henry-soria.com/my-mod/?query=value).
     *
     * @return string $sPageUrl The new clean URL.
     */
    public static function cleanDynamicUrl($sVar)
    {
        $sCurrentUrl = PH7_URL_PROT . PH7_DOMAIN . (new HttpRequest)->getUri();
        $sUrl = preg_replace(self::REGEX_URL_QUESTION_MARKS, '', $sCurrentUrl);

        if (self::areParametersInUrlFound($sCurrentUrl)) {
            return $sUrl . self::getUrlSlug($sCurrentUrl) . '&amp;' . $sVar . '=';
        }

        return $sUrl . self::trailingSlash($sUrl) . '?' . $sVar . '=';
    }

    /**
     * @param int $iTotalItems
     * @param int $iNbItemsPerPage
     *
     * @return void
     */
    private function totalPages($iTotalItems, $iNbItemsPerPage)
    {
        $this->iTotalItems = (int)$iTotalItems;
        $this->iNbItemsPerPage = (int)$iNbItemsPerPage; // or intval() function, but it is slower than casting
        $this->iCurrentPage = $this->oHttpRequest->getExists(Pagination::REQUEST_PARAM_NAME) ? $this->oHttpRequest->get(Pagination::REQUEST_PARAM_NAME, 'int') : 1;

        // Ternary condition to prevent division by zero
        $this->iTotalPages = ($this->iTotalItems !== 0 && $this->iNbItemsPerPage !== 0) ? (int)ceil($this->iTotalItems / $this->iNbItemsPerPage) : 0;

        $this->iFirstItem = (int)($this->iCurrentPage - 1) * $this->iNbItemsPerPage;
    }

    /**
     * Returns a trailing slash if needed.
     *
     * @param string $sUrl
     *
     * @return string
     */
    private static function trailingSlash($sUrl)
    {
        return substr($sUrl, -1) !== PH7_SH && !strstr($sUrl, PH7_PAGE_EXT) ? PH7_SH : '';
    }

    /**
     * @param string $sCurrentUrl
     *
     * @return bool
     */
    private static function areParametersInUrlFound($sCurrentUrl)
    {
        return preg_match(self::REGEX_URL_PARAMS, $sCurrentUrl);
    }

    /**
     * @param string $sCurrentUrl
     *
     * @return string
     */
    private static function getUrlSlug($sCurrentUrl)
    {
        $sGlueName = sprintf('&amp;%s=', Pagination::REQUEST_PARAM_NAME);

        return strpos($sCurrentUrl, $sGlueName) ?
            strstr(strrchr($sCurrentUrl, '?'), $sGlueName, true) :
            strrchr($sCurrentUrl, '?');
    }
}
