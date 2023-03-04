<?php
/**
 * @title            Page Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Page
 */

declare(strict_types=1);

namespace PH7\Framework\Page;

defined('PH7') or exit('Restricted access');

use Exception;
use PH7\Framework\Http\Http;
use PH7\Framework\Navigation\Browser;
use PH7\JustHttp\StatusCode;

class Page
{
    /**
     * Private constructor to prevent instantiation of class since it's a static class.
     */
    private function __construct()
    {
    }

    public static function maintenance(int $iMaintenanceTime): void
    {
        Http::setMaintenanceCode($iMaintenanceTime);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Maintenance page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/maintenance.html.php';

        // Stop script
        exit;
    }

    public static function message(string $sMsg): void
    {
        // Inclusion of the HTML Message page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/msg.html.php';

        // Stop script
        exit;
    }

    /**
     * Set IP address banned page.
     */
    public static function banned(): void
    {
        // Set the "forbidden" status code
        Http::setHeadersByCode(StatusCode::FORBIDDEN);

        // Inclusion of the HTML IP Banned page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/other/banned.html.php';

        // Stop script
        exit;
    }

    public static function exception(Exception $oExcept): void
    {
        // Set 500 HTTP status code
        Http::setHeadersByCode(StatusCode::INTERNAL_SERVER_ERROR);

        // Prevent caching in the browser
        (new Browser)->noCache();

        // Inclusion of the HTML Exception page
        include PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_DEFAULT_THEME . '/tpl/error/except.html.php';
    }

    public static function error500(): void
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
