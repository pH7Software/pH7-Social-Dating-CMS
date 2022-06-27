<?php
/**
 * @desc             Various Page methods with also the pagination methods.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Navigation
 */

namespace PH7\Framework\Navigation;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http as HttpRequest;

class Page
{
    private const DEFAULT_NUMBER_ITEMS = 10;

    private const REGEX_URL_PARAMS = '#\?(.+[^\./])=(.+[^\./])$#';
    private const REGEX_URL_QUESTION_MARKS = '#\?.+$#';

    private HttpRequest $oHttpRequest;

    private int $iTotalPages;

    private int $iTotalItems;

    private int $iNbItemsPerPage;

    private int $iCurrentPage;

    private int $iFirstItem;

    public function __construct()
    {
        $this->oHttpRequest = new HttpRequest;
    }

    public function getTotalPages(?int $iTotalItems, int $iNbItemsPerPage = self::DEFAULT_NUMBER_ITEMS): int
    {
        $this->totalPages($iTotalItems, $iNbItemsPerPage);

        return ($this->iTotalPages < 1) ? 1 : $this->iTotalPages;
    }

    public function getTotalItems(): int
    {
        return $this->iTotalItems;
    }

    public function getFirstItem(): int
    {
        return max($this->iFirstItem, 0);
    }

    public function getNbItemsPerPage(): int
    {
        return $this->iNbItemsPerPage;
    }

    public function getCurrentPage(): int
    {
        return $this->iCurrentPage;
    }

    /**
     * Clean Dynamic URL.
     *
     * @param string $sVar The Query URL (e.g. www.pierre-henry-soria.com/my-mod/?query=value).
     *
     * @return string $sPageUrl The new cleaned URL.
     */
    public static function cleanDynamicUrl(string $sVar): string
    {
        $sCurrentUrl = PH7_URL_PROT . PH7_DOMAIN . (new HttpRequest)->getUri();
        $sCurrentUrl = htmlspecialchars($sCurrentUrl, ENT_QUOTES);
        $sUrl = preg_replace(self::REGEX_URL_QUESTION_MARKS, '', $sCurrentUrl);

        if (self::areParametersInUrlFound($sCurrentUrl)) {
            return $sUrl . self::getUrlSlug($sCurrentUrl) . '&amp;' . $sVar . '=';
        }

        return $sUrl . self::trailingSlash($sUrl) . '?' . $sVar . '=';
    }

    private function totalPages(?int $iTotalItems, int $iNbItemsPerPage): void
    {
        $this->iTotalItems = (int)$iTotalItems; // or intval() function, but it is slower than casting
        $this->iNbItemsPerPage = $iNbItemsPerPage;
        $this->iCurrentPage = $this->oHttpRequest->getExists(Pagination::REQUEST_PARAM_NAME) ? $this->oHttpRequest->get(Pagination::REQUEST_PARAM_NAME, 'int') : 1;

        // Ternary condition to prevent division by zero
        $this->iTotalPages = ($this->iTotalItems !== 0 && $this->iNbItemsPerPage !== 0) ? (int)ceil($this->iTotalItems / $this->iNbItemsPerPage) : 0;

        $this->iFirstItem = (int)($this->iCurrentPage - 1) * $this->iNbItemsPerPage;
    }

    /**
     * Returns a trailing slash if needed.
     */
    private static function trailingSlash(string $sUrl): string
    {
        return substr($sUrl, -1) !== PH7_SH && !strstr($sUrl, PH7_PAGE_EXT) ? PH7_SH : '';
    }

    private static function areParametersInUrlFound(string $sCurrentUrl): bool
    {
        return (bool)preg_match(self::REGEX_URL_PARAMS, $sCurrentUrl);
    }

    private static function getUrlSlug(string $sCurrentUrl): string
    {
        $sGlueName = sprintf('&amp;%s=', Pagination::REQUEST_PARAM_NAME);

        return strpos($sCurrentUrl, $sGlueName) ?
            strstr(strrchr($sCurrentUrl, '?'), $sGlueName, true) :
            strrchr($sCurrentUrl, '?');
    }
}
