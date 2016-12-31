<?php
/**
 * @title          Server Class
 * @desc           This class is used to manage settings of the web server and can simulate a server secure and reliable.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / Framework / Server
 * @version        1.0
 */

namespace PH7\Framework\Server;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Kernel, PH7\Framework\Url\Uri;

final class Server
{

    const
    SERVER_PORT = 'SERVER_PORT',
    SERVER_PROTOCOL = 'SERVER_PROTOCOL',
    SERVER_NAME = 'SERVER_NAME',
    SERVER_ADDR = 'SERVER_ADDR',
    LOCAL_ADDR = 'LOCAL_ADDR',
    HTTPS = 'HTTPS',
    HTTP_HOST = 'HTTP_HOST',
    HTTP_X_FORWARDED_HOST = 'HTTP_X_FORWARDED_HOST',
    REMOTE_ADDR = 'REMOTE_ADDR',
    HTTP_CLIENT_IP = 'HTTP_CLIENT_IP',
    HTTP_X_FORWARDED_FOR = 'HTTP_X_FORWARDED_FOR',
    AUTH_USER = 'PHP_AUTH_USER',
    AUTH_PW = 'PHP_AUTH_PW',
    CURRENT_FILE = 'PHP_SELF',
    REQUEST_METHOD = 'REQUEST_METHOD',
    REQUEST_URI = 'REQUEST_URI',
    QUERY_STRING = 'QUERY_STRING',
    HTTP_ACCEPT = 'HTTP_ACCEPT',
    HTTP_ACCEPT_LANGUAGE = 'HTTP_ACCEPT_LANGUAGE',
    HTTP_ACCEPT_ENCODING = 'HTTP_ACCEPT_ENCODING',
    HTTP_X_WAP_PROFILE = 'HTTP_X_WAP_PROFILE',
    HTTP_PROFILE = 'HTTP_PROFILE',
    HTTP_USER_AGENT = 'HTTP_USER_AGENT',
    HTTP_REFERER = 'HTTP_REFERER',
    HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';

    public function __construct()
    {
        /*** Copyright ***/
        // Especially not to use the header_remove(); function no arguments if the sessions do not work correctly
        header('Server: ' . Kernel::SOFTWARE_SERVER_NAME);
        header('X-Powered-By: ' . Kernel::SOFTWARE_TECHNOLOGY_NAME);
        header('X-Content-Encoded-By: ' . Kernel::SOFTWARE_COMPANY . ' - ' . Kernel::SOFTWARE_NAME . ' ' . Kernel::SOFTWARE_VERSION . ' Build ' . Kernel::SOFTWARE_BUILD);
    }

    /**
     * Check to see if we are on a Windows server.
     *
     * @return boolean TRUE if windows, FALSE if not.
     */
    public static function isWindows()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }

    /**
     * Check to see if we are on a Unix server.
     *
     * @return boolean TRUE if Unix, FALSE if not.
     */
    public static function isUnix()
    {
        $sOS = strtoupper(PHP_OS);
        return ($sOS === 'UNIX' || $sOS === 'LINUX' || $sOS === 'FREEBSD' || $sOS === 'OPENBSD');
    }

    /**
     * Check to see if we are on a Mac OS server.
     *
     * @return boolean TRUE if windows, FALSE if not.
     */
    public static function isMac()
    {
        return strtoupper(substr(PHP_OS, 0, 3) === 'MAC');
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
     * Get the IP address of server.
     *
     * @internal We use LOCAL_ADDR variable for compatibility with Windows servers.
     * @return string IP address.
     */
    public static function getIp()
    {
        return self::getVar(self::SERVER_ADDR, self::getVar(self::LOCAL_ADDR, gethostbyname(self::getName())));
    }

    /**
     * Check if the server is in local.
     *
     * @return boolean TRUE if it is in local mode, FALSE if not.
     */
    public function isLocalHost()
    {
        $sServerName = self::getName();
        $sHttpHost = self::getVar(self::HTTP_HOST);
        return ($sServerName === 'localhost' || $sServerName === '127.0.0.1' || $sHttpHost === 'localhost' || $sHttpHost === '127.0.0.1');
    }

    /**
     * Retrieve a member of the $_SERVER super global.
     *
     * @param string $sKey If NULL, returns the entire $_SERVER variable. Default NULL
     * @param mixed $sDefVal The value to use if server key is not found. Default NULL
     * @return mixed (string | array | null)
     */
    public static function getVar($sKey = null, $sDefVal = null)
    {
        if (null === $sKey) return $_SERVER;

        return (!empty($_SERVER[$sKey])) ? htmlspecialchars($_SERVER[$sKey], ENT_QUOTES) : $sDefVal;
    }

    /**
     * Check if Apache's mod_rewrite is installed.
     *
     * @return boolean
    */
    public static function isRewriteMod()
    {
        // Check if mod_rewrite is installed and is configured to be used via .htaccess
        if (!$bIsRewrite = (strtolower(getenv('HTTP_MOD_REWRITE')) == 'on'))
        {
            $sOutputMsg = 'mod_rewrite Works!';

            if (Uri::getInstance()->fragment(0) == 'test_mod_rewrite')
                exit($sOutputMsg);

            $sPage = @file_get_contents(PH7_URL_ROOT . 'test_mod_rewrite');

            $bIsRewrite = ($sPage == $sOutputMsg);
        }

        return $bIsRewrite;
    }

    /**
     * Alias method of the checkInternetConnection() function (located in ~/_protected/app/includes/helpers/misc.php).
     *
     * @return boolean Returns TRUE if the Internet connection is enabled, FALSE otherwise.
     */
    public static function checkInternetConnection()
    {
        return \PH7\is_internet();
    }

}
