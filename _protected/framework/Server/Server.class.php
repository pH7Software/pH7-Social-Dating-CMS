<?php
/**
 * @title          Server Class
 * @desc           This class is used to manage settings of the web server and can simulate a server secure and reliable.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Server
 */

namespace PH7\Framework\Server;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel;
use PH7\Framework\Url\Uri;
use function PH7\is_internet;

final class Server
{
    const SERVER_PORT = 'SERVER_PORT';
    const SERVER_PROTOCOL = 'SERVER_PROTOCOL';
    const SERVER_NAME = 'SERVER_NAME';
    const SERVER_ADDR = 'SERVER_ADDR';
    const LOCAL_ADDR = 'LOCAL_ADDR';
    const HTTPS = 'HTTPS';
    const HTTP_HOST = 'HTTP_HOST';
    const HTTP_X_FORWARDED_HOST = 'HTTP_X_FORWARDED_HOST';
    const REMOTE_ADDR = 'REMOTE_ADDR';
    const HTTP_CLIENT_IP = 'HTTP_CLIENT_IP';
    const HTTP_X_FORWARDED_FOR = 'HTTP_X_FORWARDED_FOR';
    const AUTH_USER = 'PHP_AUTH_USER';
    const AUTH_PW = 'PHP_AUTH_PW';
    const CURRENT_FILE = 'PHP_SELF';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const REQUEST_URI = 'REQUEST_URI';
    const QUERY_STRING = 'QUERY_STRING';
    const HTTP_ACCEPT = 'HTTP_ACCEPT';
    const HTTP_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE';
    const HTTP_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING';
    const HTTP_X_WAP_PROFILE = 'HTTP_X_WAP_PROFILE';
    const HTTP_PROFILE = 'HTTP_PROFILE';
    const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    const HTTP_REFERER = 'HTTP_REFERER';
    const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';

    const LOCAL_IP = '127.0.0.1';
    const LOCAL_HOSTNAME = 'localhost';

    const UNIX_OS = [
        'UNIX',
        'LINUX',
        'FREEBSD',
        'OPENBSD'
    ];

    public function __construct()
    {
        header('Server: ' . Kernel::SOFTWARE_SERVER_NAME);
        header('X-Powered-By: ' . Kernel::SOFTWARE_TECHNOLOGY_NAME);
        header('X-Content-Encoded-By: ' . Kernel::SOFTWARE_NAME . ' - ' . Kernel::SOFTWARE_COMPANY . ' ' . Kernel::SOFTWARE_VERSION . ' Build ' . Kernel::SOFTWARE_BUILD);
    }

    /**
     * Check to see if we are on a Windows server.
     *
     * @return bool TRUE if windows, FALSE if not.
     */
    public static function isWindows()
    {
        return 0 === stripos(PHP_OS, 'WIN');
    }

    /**
     * See if we are on a Unix server...?
     *
     * @return bool TRUE if Unix, FALSE if not.
     */
    public static function isUnix()
    {
        $sOS = strtoupper(PHP_OS);

        return in_array($sOS, self::UNIX_OS, true);
    }

    /**
     * Check to see if we are on a Mac OS server.
     *
     * @return bool TRUE if windows, FALSE if not.
     */
    public static function isMac()
    {
        return 0 === stripos(PHP_OS, 'MAC');
    }

    /**
     * Get the IP address of server.
     *
     * @internal We use LOCAL_ADDR variable for compatibility with Windows servers.
     *
     * @return string IP address.
     */
    public static function getIp()
    {
        return self::getVar(
            self::SERVER_ADDR,
            self::getVar(self::LOCAL_ADDR, gethostbyname(self::getName()))
        );
    }

    /**
     * Retrieve a member of the $_SERVER super global.
     *
     * @param string|null $sKey If NULL, returns the entire $_SERVER variable.
     * @param string|null $sDefVal A default value to use if server key is not found.
     *
     * @return string|array|null
     */
    public static function getVar($sKey = null, $sDefVal = null)
    {
        if (null === $sKey) {
            return $_SERVER;
        }

        return !empty($_SERVER[$sKey]) ? htmlspecialchars($_SERVER[$sKey], ENT_QUOTES) : $sDefVal;
    }

    /**
     * Get the server name.
     *
     * @return string
     */
    public static function getName()
    {
        return self::getVar(self::SERVER_NAME);
    }

    /**
     * Check if the server is in local.
     *
     * @return bool TRUE if it is in local mode, FALSE if not.
     */
    public static function isLocalHost()
    {
        $sServerName = self::getName();
        $sHttpHost = self::getVar(self::HTTP_HOST);

        return ($sServerName === self::LOCAL_HOSTNAME || $sServerName === self::LOCAL_IP ||
            $sHttpHost === self::LOCAL_HOSTNAME || $sHttpHost === self::LOCAL_IP);
    }

    /**
     * Check if Apache's mod_rewrite is installed.
     *
     * @return bool
     */
    public static function isRewriteMod()
    {
        // Check if mod_rewrite is installed and is configured to be used via .htaccess
        if (!strtolower(getenv('HTTP_MOD_REWRITE')) === 'on') {
            $sOutputMsg = 'mod_rewrite Works!';

            if (Uri::getInstance()->fragment(0) === 'test_mod_rewrite') {
                exit($sOutputMsg);
            }

            $sPage = @file_get_contents(PH7_URL_ROOT . 'test_mod_rewrite');
            return $sPage === $sOutputMsg;
        }

        return true;
    }

    /**
     * Alias method of the checkInternetConnection() function (located in ~/_protected/app/includes/helpers/misc.php).
     *
     * @return bool Returns TRUE if the Internet connection is enabled, FALSE otherwise.
     */
    public static function checkInternetConnection()
    {
        return is_internet();
    }

    /**
     * @return bool
     */
    public static function isHttps()
    {
        return substr(PH7_URL_PROT, 0, 5) === 'https';
    }
}
