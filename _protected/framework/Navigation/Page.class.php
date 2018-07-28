<?php
/**
 * @title            Page Class
 * @desc             Various Page methods with also the pagination methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Navigation
 * @version          1.2
 */

namespace PH7\Framework\Navigation;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;

class Page
{
    const DEFAULT_NUMBER_ITEMS = 10;

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
     * @param int $iNbItemsPerPage
     *
     * @return void
     */
    protected function totalPages($iTotalItems, $iNbItemsPerPage)
    {
        $this->iTotalItems = (int)$iTotalItems;
        $this->iNbItemsPerPage = (int)$iNbItemsPerPage; // or intval() function, but it is slower than casting
        $this->iCurrentPage = (int)$this->oHttpRequest->getExists('p') ? $this->oHttpRequest->get('p') : 1;

        // Ternary condition to prevent division by zero
        $this->iTotalPages = (int)($this->iTotalItems !== 0 && $this->iNbItemsPerPage !== 0) ? ceil($this->iTotalItems / $this->iNbItemsPerPage) : 0;

        $this->iFirstItem = (int)($this->iCurrentPage - 1) * $this->iNbItemsPerPage;
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
        $sCurrentUrl = (new HttpRequest)->currentUrl();
        $sUrl = preg_replace('#\?.+$#', '', $sCurrentUrl);

        if (preg_match('#\?(.+[^\./])=(.+[^\./])$#', $sCurrentUrl)) {
            return $sUrl . self::getUrlSlug($sCurrentUrl) . '&amp;' . $sVar . '=';
        }

        return $sUrl . self::trailingSlash($sUrl) . '?' . $sVar . '=';
    }

    /**
     * Returns a trailing slash if needed.
     *
     * @param  string $sUrl
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
     * @return string
     */
    private static function getUrlSlug($sCurrentUrl)
    {
        return strpos($sCurrentUrl, '&amp;') !== false ? strrchr($sCurrentUrl, '?') !== false : strrchr($sCurrentUrl, '?');
    }
}
