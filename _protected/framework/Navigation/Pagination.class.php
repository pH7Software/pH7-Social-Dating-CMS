<?php
/**
 * @title            Pagination Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Page
 * @version          1.0
 */

namespace PH7\Framework\Navigation;
defined('PH7') or exit('Restricted access');

class Pagination
{

    private $_sPageName, $_iTotalPages, $_iCurrentPage, $_iShowItems, $_sHtmlOutput;

    private $_aOptions = [
        'range'               => 3, // Number of items to display on each side of the current page
        'text_first_page'     => '&laquo;', // Button text "First Page"
        'text_last_page'      => '&raquo;', // Button text "Last Page"
        'text_next_page'      => '&rsaquo;', //  Button text "Next"
        'text_previous_page'  => '&lsaquo;' // Button text "Previous"
    ];

    /**
     * Constructor
     *
     * @param integer $iTotalPages
     * @param integer $iCurrentPage
     * @param string $sPageName Default 'p'
     * @param array $aOptions Optional options.
     */
    public function __construct($iTotalPages, $iCurrentPage, $sPageName = 'p', array $aOptions = array())
    {
        // Set the total number of page
        $this->_iTotalPages = $iTotalPages;

        // Retrieve the number of the current page
        $this->_iCurrentPage = $iCurrentPage;

        // Put options update
        $this->_aOptions += $aOptions;

        // It retrieves the address of the page
        $this->_sPageName = Page::cleanDynamicUrl($sPageName);


        // Management pages to see
        $this->_iShowItems = ($this->_aOptions['range'] * 2) + 1;

        // It generates the paging
        $this->_generate();
    }

    /**
     * Display the pagination if there is more than one page
     *
     * @return string Html code.
     */
    public function getHtmlCode()
    {
        return $this->_sHtmlOutput;
    }

    /**
     * Generate the HTML pagination code.
     *
     * @return void
     */
    private function _generate()
    {
        // If you have more than one page, it displays the navigation
        if ($this->_iTotalPages > 1)
        {
            $this->_sHtmlOutput = '<div class="clear"></div><nav class="center" role="navigation"><ul class="pagination">';

            // Management link to go to the first page
            if ($this->_aOptions['text_first_page'])
            {
                if ($this->_iCurrentPage > 2 && $this->_iCurrentPage > $this->_aOptions['range']+1 && $this->_iShowItems < $this->_iTotalPages)
                    $this->_sHtmlOutput .= '<li><a href="' . $this->_sPageName . '1"><span aria-hidden="true">' . $this->_aOptions['text_first_page'] . '</span></a></li>';
            }

            // Management the Previous link
            if ($this->_aOptions['text_previous_page'])
            {
                if ($this->_iCurrentPage > 2 && $this->_iShowItems < $this->_iTotalPages)
                    $this->_sHtmlOutput .= '<li><a href="' . $this->_sPageName . ($this->_iCurrentPage-1) . '" aria-label="Previous"><span aria-hidden="true">' . $this->_aOptions['text_previous_page'] . '</span></a></li>';
            }
            // Management of other paging buttons...
            for ($i=1; $i <= $this->_iTotalPages; $i++)
            {
                if (($i >= $this->_iCurrentPage - $this->_aOptions['range'] && $i <= $this->_iCurrentPage + $this->_aOptions['range']) || $this->_iTotalPages <= $this->_iShowItems)
                    $this->_sHtmlOutput .= '<li' . ($this->_iCurrentPage == $i ? ' class="active"' : '') . '><a href="' . $this->_sPageName . $i . '">' . $i . '</a></li>';
            }

            //  Management the "Next" link
            if ($this->_aOptions['text_next_page'])
            {
                if ($this->_iCurrentPage < $this->_iTotalPages - 1 && $this->_iShowItems < $this->_iTotalPages)
                    $this->_sHtmlOutput .= '<li><a href="' . $this->_sPageName . ($this->_iCurrentPage+1) . '" aria-label="Next"><span aria-hidden="true">' . $this->_aOptions['text_next_page'] . '</span></a></li>';
            }

            // Management link to go to the last page
            if ($this->_aOptions['text_last_page'])
            {
                if ($this->_iCurrentPage < $this->_iTotalPages-1 && $this->_iCurrentPage + $this->_aOptions['range'] < $this->_iTotalPages && $this->_iShowItems < $this->_iTotalPages)
                    $this->_sHtmlOutput .= '<li><a href="' . $this->_sPageName . $this->_iTotalPages . '"><span aria-hidden="true">' . $this->_aOptions['text_last_page'] . '</span></a></li>';
            }

            $this->_sHtmlOutput .= '</ul></nav>';
        }
    }
}
