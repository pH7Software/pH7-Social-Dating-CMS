<?php
/**
 * @title            Kernel Class
 * @desc             Kernel Class of pH7CMS.
 *
 * @author           Pierre-Henry Soria <pierre@soria.pw>
 * @copyright        (c) 2011-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          CC-BY License - http://creativecommons.org/licenses/by/3.0/
 * @link             http://ph7cms.com
 * @package          PH7 / Framework / Core
 * @version          1.6
 */

namespace PH7\Framework\Core;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Config\Config;
use PH7\Framework\File\File;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Navigation\Browser;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security\Version;
use PH7\Framework\Str\Str;

abstract class Kernel
{
    const SOFTWARE_NAME = 'pH7CMS';
    const SOFTWARE_DESCRIPTION = 'pH7CMS Dating Web App Builder. The ONLY Free, Open Source, Pro Dating Startup Builder for Growing Your Online Business';
    const SOFTWARE_WEBSITE = 'https://ph7cms.com';
    const SOFTWARE_DOC_URL = 'https://ph7cms.com/doc';
    const SOFTWARE_GIT_REPO_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    const SOFTWARE_ISSUE_URL = self::SOFTWARE_GIT_REPO_URL . '/issues';
    const SOFTWARE_RELEASE_URL = self::SOFTWARE_GIT_REPO_URL . '/releases';
    const SOFTWARE_REVIEW_URL = 'https://sourceforge.net/projects/ph7socialdating/reviews/';
    const PATREON_URL = 'https://www.patreon.com/bePatron?u=3534366';
    const SOFTWARE_EMAIL = 'hello@ph7cms.com';
    const SOFTWARE_TWITTER = '@pH7Soft';
    const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    const SOFTWARE_COMPANY = 'Web Engineer: Pierre-Henry Soria';
    const SOFTWARE_LICENSE = 'GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.';
    const SOFTWARE_COPYRIGHT = '(c) 2011-%s, Pierre-Henry Soria. All Rights Reserved.';
    const SOFTWARE_VERSION_NAME = Version::KERNEL_VERSION_NAME;
    const SOFTWARE_VERSION = Version::KERNEL_VERSION;
    const SOFTWARE_BUILD = Version::KERNEL_BUILD;
    const SOFTWARE_TECHNOLOGY_NAME = Version::KERNEL_TECHNOLOGY_NAME;
    const SOFTWARE_SERVER_NAME = Version::KERNEL_SERVER_NAME;
    const SOFTWARE_USER_AGENT = 'pH7 Web Simulator/1.1.2'; // USER AGENT NAME of Web Simulator
    const SOFTWARE_CRAWLER_NAME = 'ph7crawler'; // CRAWLER BOT NAME

    /** @var Config */
    protected $config;

    /** @var Str */
    protected $str;

    /** @var File */
    protected $file;

    /** @var Http */
    protected $httpRequest;

    /** @var Browser */
    protected $browser;

    /** @var Registry */
    protected $registry;

    public function __construct()
    {
        $this->config = Config::getInstance();
        $this->str = new Str;
        $this->file = new File;
        $this->httpRequest = new Http;
        $this->browser = new Browser;
        $this->registry = Registry::getInstance();
    }

    /**
     * Clone is set to private to stop cloning.
     */
    private function __clone()
    {
    }
}
