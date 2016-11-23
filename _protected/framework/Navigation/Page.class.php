<?php
/**
 * @title            Page Class
 * @desc             Various Page methods with also the pagination methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Navigation
 * @version          1.2
 */

namespace PH7\Framework\Navigation;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

class Page
{
    private $_oHttpRequest, $_iTotalPages, $_iTotalItems, $_iNbItemsByPage, $_iCurrentPage, $_iFirstItem;

    public function __construct()
    {
        $this->_oHttpRequest = new Http;
    }


    /***** Methods for preparing the paging system *****/

    /**
     * @access protected
     * @param integer $iTotalItems
     * @param integer $iNbItemsByPage
     * @return void
     */
    protected function totalPages($iTotalItems, $iNbItemsByPage)
    {
        $this->_iTotalItems = (int) $iTotalItems;
        $this->_iNbItemsByPage = (int) $iNbItemsByPage; // or intval() function, but it is slower than the cast
        $this->_iCurrentPage = (int) ($this->_oHttpRequest->getExists('p')) ? $this->_oHttpRequest->get('p') : 1;
        $this->_iTotalPages = (int) ($this->_iTotalItems !== 0 && $this->_iNbItemsByPage !== 0) ? ceil($this->_iTotalItems / $this->_iNbItemsByPage) : 0; // Ternary condition to prevent division by zero
        $this->_iFirstItem = (int) ($this->_iCurrentPage-1) * $this->_iNbItemsByPage;
    }

    /**
     * @param integer $iTotalItems
     * @param integer $iNbItemsByPage Default 10
     * @return integer The number of pages.
     */
    public function getTotalPages($iTotalItems, $iNbItemsByPage = 10)
    {
        $this->totalPages($iTotalItems, $iNbItemsByPage);
        return ($this->_iTotalPages < 1) ? 1 : $this->_iTotalPages;
    }

    public function getTotalItems()
    {
        return $this->_iTotalItems;
    }

    public function getFirstItem()
    {
        return ($this->_iFirstItem < 0) ? 0 : $this->_iFirstItem;
    }

    public function getNbItemsByPage()
    {
        return $this->_iNbItemsByPage;
    }

    public function getCurrentPage()
    {
        return $this->_iCurrentPage;
    }

    /**
     * Clean a Dynamic URL for some features CMS.
     *
     * @static
     * @param string $sVar The Query URL (e.g. www.pierre-henry-soria.com/my-mod/?query=value).
     * @return string $sPageUrl The new clean URL.
     */
    public static function cleanDynamicUrl($sVar)
    {
        $sCurrentUrl = (new Http)->currentUrl();
        $sUrl = preg_replace('#\?.+$#', '', $sCurrentUrl);

        if (preg_match('#\?(.+[^\./])=(.+[^\./])$#', $sCurrentUrl))
        {
            $sUrlSlug = (strpos($sCurrentUrl, '&amp;')) ? strrchr($sCurrentUrl, '?') : strrchr($sCurrentUrl, '?');
            $sPageUrl = $sUrl . $sUrlSlug . '&amp;' . $sVar . '=';
        }
        else
        {
            $sPageUrl = $sUrl . static::trailingSlash($sUrl) . '?' . $sVar . '=';
        }

        return $sPageUrl;
    }

    /**
     * Returns a trailing slash if needed.
     *
     * @static
     * @param  string $sUrl
     * @return string
     */
    protected static function trailingSlash($sUrl)
    {
        return (substr($sUrl, -1) !== PH7_SH && !strstr($sUrl, PH7_PAGE_EXT)) ? PH7_SH : '';
    }
}
