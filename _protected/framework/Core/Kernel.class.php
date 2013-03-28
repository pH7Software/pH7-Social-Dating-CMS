<?php
/**
 * @title            Kernel Class
 * @desc             Kernel Class of the CMS.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2011-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Core
 * @version          0.8
 */

namespace PH7\Framework\Core;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Config\Config,
PH7\Framework\Str\Str,
PH7\Framework\File\File,
PH7\Framework\Mvc\Request\HttpRequest,
PH7\Framework\Navigation\Browser,
PH7\Framework\Registry\Registry;

abstract class Kernel
{

    const
    SOFTWARE_NAME = '¡pH7! Social Dating CMS',
    SOFTWARE_DESCRIPTION = 'This builder community dating software for web 3.0 new generation!',
    SOFTWARE_WEBSITE = 'http://software.hizup.com',
    SOFTWARE_EMAIL = 'support.software@hizup.com',
    SOFTWARE_AUTHOR = 'Pierre-Henry Soria',
    SOFTWARE_COMPANY = 'pH7 Framework / Social CMS (Pierre-Henry Soria)',
    SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.',
    SOFTWARE_COPYRIGHT = '© (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.',
    SOFTWARE_VERSION_NAME = 'pOH',
    SOFTWARE_VERSION = '0.8.9',
    SOFTWARE_BUILD = '1',
    SOFTWARE_NAME_TECHNOLOGY = 'pH7T/1.0.1', // Ph7 Technology
    SOFTWARE_NAME_SERVER = 'pH7WS/1.0.0', // pH7 Web Server
    SOFTWARE_USER_AGENT = 'pH7 Web Simulator/1.1.2', // USER AGENT NAME of Web Simulator
    SOFTWARE_CRAWLER_NAME = 'ph7hizupcrawler'; // CRAWLER BOT NAME

    protected $config, $str, $file, $httpRequest, $browser, $registry;

    public function __construct()
    {
        // Check license
        $oLicense = new License;
        if(!defined( 'PH7_LICENSE_STATUS' )) define( 'PH7_LICENSE_STATUS', $oLicense->check()->licenseStatus() );
        if(!defined( 'PH7_LICENSE_NO_COPYRIGHT' )) define( 'PH7_LICENSE_NO_COPYRIGHT', $oLicense->check()->noCopyrightStatus() );
        unset($oLicense);

        if(!PH7_LICENSE_STATUS)
        {
            echo t('Sorry, your License Key is incorrect! Please go <a href="%0%">HiZup Software</a> to get a valid license key.', self::SOFTWARE_WEBSITE);
            exit(1);
        }

        $this->config = Config::getInstance();
        $this->str = new Str;
        $this->file = new File;
        $this->httpRequest = new HttpRequest;
        $this->browser = new Browser;
        $this->registry = Registry::getInstance();
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
