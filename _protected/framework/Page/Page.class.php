<?php
/**
 * @title            Page Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Page
 */

namespace PH7\Framework\Page;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Http\Http;
use PH7\Framework\Navigation\Browser;
use Teapot\StatusCode;

class Page
{
    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    /**
     * Set a maintenance page.
     *
     * @param int $iMaintenanceTime Time in seconds that the site will be down for maintenance.
     *
     * @return void
     */
    public static function maintenance($iMaintenanceTime)
    {
        Http::setMaintenanceCode($iMaintenanceTime);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Maintenance page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/maintenance.html.php';

        // Stop script
        exit;
    }

    /**
     * Set a message page.
     *
     * @param string $sMsg Information message.
     *
     * @return void
     */
    public static function message($sMsg)
    {
        // Inclusion of the HTML Message page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/msg.html.php';

        // Stop script
        exit;
    }

    /**
     * Set IP address banned page.
     *
     * @return void
     */
    public static function banned()
    {
        // Set the "forbidden" status code
        Http::setHeadersByCode(StatusCode::FORBIDDEN);

        // Inclusion of the HTML IP Banned page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/banned.html.php';

        // Stop script
        exit;
    }

    /**
     * Set exception page.
     *
     * @param \Exception $oExcept
     *
     * @return void
     */
    public static function exception(\Exception $oExcept)
    {
        // Set 500 HTTP status code
        Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Exception page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/error/except.html.php';
    }

    /**
     * Set error 500 page.
     *
     * @return void
     */
    public static function error500()
    {
        // Set 500 HTTP status code
        Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Internal Server Error page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/error/500.html.php';

        // Stop script
        exit;
    }
}
