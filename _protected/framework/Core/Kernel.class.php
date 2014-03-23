<?php
/**
 * @title            Kernel Class
 * @desc             Kernel Class of the pH7CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @link             http://software.hizup.com
 * @package          PH7 / Framework / Core
 * @version          1.1.0
 */

namespace PH7\Framework\Core;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Config\Config,
PH7\Framework\Str\Str,
PH7\Framework\File\File,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Navigation\Browser,
PH7\Framework\Registry\Registry,
PH7\Framework\Page\Page,
PH7\Framework\Server\Server;

abstract class Kernel
{

    const
    SOFTWARE_NAME = 'pH7CMS',
    SOFTWARE_DESCRIPTION = 'This builder community dating software for web 3.0 new generation!',
    SOFTWARE_WEBSITE = 'http://software.hizup.com',
    SOFTWARE_LICENSE_KEY_URL = 'https://hizup.net/clients/license',
    SOFTWARE_HELP_URL = 'https://hizup.net/clients/support', // Help Desk Support URL
    SOFTWARE_DOC_URL = 'http://software.hizup.com/doc',
    SOFTWARE_FAQ_URL = 'http://software.hizup.com/faq',
    SOFTWARE_FORUM_URL = 'http://software.hizup.com/forum',
    SOFTWARE_EMAIL = 'ph7software@gmail.com',
    SOFTWARE_AUTHOR = 'Pierre-Henry Soria',
    SOFTWARE_COMPANY = 'pH7 Framework / Social CMS (Pierre-Henry Soria)',
    SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.',
    SOFTWARE_COPYRIGHT = '© (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.',
    SOFTWARE_VERSION_NAME = 'pOH',
    SOFTWARE_VERSION = '1.1.0',
    SOFTWARE_BUILD = '1',
    SOFTWARE_NAME_TECHNOLOGY = 'pH7T/1.0.1', // Ph7 Technology
    SOFTWARE_NAME_SERVER = 'pH7WS/1.0.0', // pH7 Web Server
    SOFTWARE_USER_AGENT = 'pH7 Web Simulator/1.1.2', // USER AGENT NAME of Web Simulator
    SOFTWARE_CRAWLER_NAME = 'ph7hizupcrawler'; // CRAWLER BOT NAME

    protected $config, $str, $file, $httpRequest, $browser, $registry;

    public function __construct()
    {
        //** Temporary code
        if (!Server::isRewriteMod())
        {
            $sModRewriteMsg = '<span style="font-weight:bold;color:red">pH7CMS requires Apache "mod_rewrite".</span><br /> Please install it so pH7CMS can works.<br /> Click <a href="http://software.hizup.com/doc/en/how-to-install-rewrite-module" target="_blank">here</a> if you want to get more information on how to install the rewrite module.<br /><br /> After doing this, please <a href="' . PH7_URL_ROOT . '">retry</a>.';
            Page::message($sModRewriteMsg);
        }
        //*/

        $this->_checkLicense();

        $this->config = Config::getInstance();
        $this->str = new Str;
        $this->file = new File;
        $this->httpRequest = new Http;
        $this->browser = new Browser;
        $this->registry = Registry::getInstance();
    }

    /**
     * Check License key.
     *
     * @final
     * @return integer Returns '1' if the license key is invalid and stops the script with the exit() function.
     */
    private final function _checkLicense()
    {
        $oLicense = new License;
        if (!defined( 'PH7_LICENSE_STATUS' )) define( 'PH7_LICENSE_STATUS', $oLicense->check()->licenseStatus() );
        if (!defined( 'PH7_LICENSE_NO_COPYRIGHT' )) define( 'PH7_LICENSE_NO_COPYRIGHT', $oLicense->check()->noCopyrightStatus() );
        unset($oLicense);

        if (!PH7_LICENSE_STATUS)
        {
            $this->_checkInternetConnection(); // First we check the Internet connection

            Page::message(t('Sorry, your <a href="%0%">pH7CMS</a> License Key is incorrect!', self::SOFTWARE_WEBSITE));
        }
    }

    /**
     * Check Internet connection.
     *
     * @final
     * @return integer Returns '1' if it is not connected to the Internet and stops the script with the exit() function.
     */
    private final function _checkInternetConnection()
    {
        if (!Server::checkInternetConnection())
        {
            Page::message(t('Your server must be connected to the Internet for pH7Framework to function properly.'));
        }
    }

    public function __destruct()
    {
        unset(
          $this->config,
          $this->str,
          $this->file,
          $this->httpRequest,
          $this->browser,
          $this->registry
        );
    }

    /**
     * Clone is set to private to stop cloning.
     *
     * @access private
     */
    private function __clone() {}

}
