<?php
/**
 * @title            Page Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Page
 * @version          1.0
 */

namespace PH7\Framework\Page;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http, PH7\Framework\Navigation\Browser;

class Page
{

    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     *
     * @access private
     */
    private function __construct() {}

    /**
     * Set a maintenance page.
     *
     * @access public
     * @static
     * @param integer $iMaintenanceTime Time site will be down for (in seconds).
     * @return void
     */
    public static function maintenance($iMaintenanceTime)
    {
        // Set the HTTP status codes for the Maintenance page
        Http::setMaintenanceCodes($iMaintenanceTime);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Maintenance page
        include PH7_PATH_SYS . 'global/views/' . PH7_DEFAULT_THEME . '/other/maintenance.html.php';

        // Stop script
        exit;
    }

    /**
     * Set a message page.
     *
     * @access public
     * @static
     * @param string $sMsg Information message.
     * @return void
     */
    public static function message($sMsg)
    {
        // Inclusion of the HTML Message page
        include PH7_PATH_SYS . 'global/views/' . PH7_DEFAULT_THEME . '/other/msg.html.php';

        // Stop script
        exit;
    }

    /**
     * Set IP address banned page.
     *
     * @access public
     * @static
     * @return void
     */
    public static function banned()
    {
        // Set the "forbidden" status code
        Http::setHeadersByCode(403);

        // Inclusion of the HTML IP Banned page
        include PH7_PATH_SYS . 'global/views/' . PH7_DEFAULT_THEME . '/other/banned.html.php';

        // Stop script
        exit;
    }

    /**
     * Set exception page.
     *
     * @access public
     * @static
     * @param object $oExcept \Exception
     * @return void
     */
    public static function exception(\Exception $oExcept)
    {
        // Set 500 HTTP status code
        Http::setHeadersByCode(500);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Exception page
        include PH7_PATH_SYS . 'global/views/' . PH7_DEFAULT_THEME . '/error/except.html.php';
    }

    /**
     * Set error 500 page.
     *
     * @access public
     * @static
     * @return void
     */
    public static function error500()
    {
        // Set 500 HTTP status code
        Http::setHeadersByCode(500);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Internal Server Error page
        include PH7_PATH_SYS . 'global/views/' . PH7_DEFAULT_THEME . '/error/500.html.php';

        // Stop script
        exit;
    }

}
