<?php
/**
 * @title            Page Class
 * @desc             Various Page methods with also the pagination methods.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Navigation
 * @version          1.2
 */

namespace PH7\Framework\Navigation;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\HttpRequest;

class Page
{

    private $_oHttpRequest, $_iTotalPages, $_iTotalItems, $_iNbItemsByPage, $_iCurrentPage, $_iFirstItem;

    public function __construct()
    {
        $this->_oHttpRequest = new HttpRequest;
    }

      /**
       * @static
       * @param float $iStartTime
       * @param float $iEndTime
       * @return float
       */
    public static function time($fStartTime, $fEndTime)
    {
        return round($fEndTime - $fStartTime, 8);
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
      $this->_iNbItemsByPage = (int) $iNbItemsByPage; // or intval() function, but it is slower than the cast
      $this->_iTotalItems = (int) $iTotalItems;
      $this->_iCurrentPage = (int) ($this->_oHttpRequest->getExists('p')) ? $this->_oHttpRequest->get('p') : 1;
      $this->_iTotalPages = (int) ceil($this->_iTotalItems/$this->_iNbItemsByPage);
      $this->iFirstItem = (int) ($this->_iCurrentPage-1) * $this->_iNbItemsByPage;
    }

    /**
     * @param integer $iTotalItems
     * @param integer $iNbItemsByPage Default 10
     * @return integer The number of pages.
     */
    public function getTotalPages($iTotalItems, $iNbItemsByPage = 10)
    {
        $this->totalPages($iTotalItems, $iNbItemsByPage);
        return ($this->_iTotalPages <1) ? 1 : $this->_iTotalPages;
    }

    public function getTotalItems()
    {
        return $this->_iTotalItems;
    }

    public function getFirstItem()
    {
        return ($this->iFirstItem < 0) ? 0 : $this->iFirstItem;
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
        $sCurrentUrl = (new HttpRequest)->currentUrl();
        $sUrl = preg_replace('#\?.+$#', '', $sCurrentUrl);

        if (preg_match('#\?(.+[^\./])=(.+[^\./])$#', $sCurrentUrl))
        {
            $sUrlSlug = (strpos($sCurrentUrl, '&amp;')) ? strstr(strrchr($sCurrentUrl, '?'), '&amp;', true) : strrchr($sCurrentUrl, '?');
            $sPageUrl = $sUrl . $sUrlSlug . '&amp;' . $sVar . '=';
        }
        else
        {
            $sIsSlash = (substr($sUrl, -1) !== '/') ? '/' : '';
            $sPageUrl = $sUrl . $sIsSlash . '?' . $sVar . '=';
        }

        return $sPageUrl;
    }

    public function __destruct()
    {
        unset(
            $this->_oHttpRequest,
            $this->_iTotalPages,
            $this->_iTotalItems,
            $this->_iNbItemsByPage,
            $this->_iCurrentPage,
            $this->_iFirstItem
        );
    }

}
