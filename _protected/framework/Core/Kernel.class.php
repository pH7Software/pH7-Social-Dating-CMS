<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2011-2026, Pierre-Henry Soria and pH7Builder contributors.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @link             https://ph7builder.com
 * @package          PH7 / Framework / Core
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
    const SOFTWARE_NAME = 'pH7Builder';
    const SOFTWARE_DESCRIPTION = 'Self-hosted, open-source software for dating sites, matchmaking communities, and social networks';
    const SOFTWARE_WEBSITE = 'https://ph7builder.com';
    const SOFTWARE_DOC_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS/tree/18.x/docs';
    const SOFTWARE_GIT_REPO_URL = 'https://github.com/pH7Software/pH7-Social-Dating-CMS';
    const SOFTWARE_ISSUE_URL = self::SOFTWARE_GIT_REPO_URL . '/issues';
    const SOFTWARE_DISCUSSION_URL = self::SOFTWARE_GIT_REPO_URL . '/discussions';
    const SOFTWARE_RELEASE_URL = self::SOFTWARE_GIT_REPO_URL . '/releases';
    const SOFTWARE_REVIEW_URL = 'https://sourceforge.net/projects/ph7socialdating/reviews/';
    const PATREON_URL = 'https://www.patreon.com/bePatron?u=3534366';
    const BUYMEACOFFEE_URL = 'https://www.buymeacoffee.com/ph7cms';
    const SOFTWARE_EMAIL = 'hello@ph7builder.com';
    const SOFTWARE_TWITTER = '@pH7Soft';
    const SOFTWARE_AUTHOR = 'Pierre-Henry Soria';
    const SOFTWARE_COMPANY = 'pH7Software';
    const SOFTWARE_COPYRIGHT = 'Copyright (c) 2011-%s, Pierre-Henry Soria and pH7Builder contributors.';
    const SOFTWARE_VERSION_NAME = Version::KERNEL_VERSION_NAME;
    const SOFTWARE_VERSION = Version::KERNEL_VERSION;
    const SOFTWARE_BUILD = Version::KERNEL_BUILD;
    const SOFTWARE_TECHNOLOGY_NAME = Version::KERNEL_TECHNOLOGY_NAME;
    const SOFTWARE_SERVER_NAME = Version::KERNEL_SERVER_NAME;

    protected Config $config;

    protected Str $str;

    protected File $file;

    protected Http $httpRequest;

    protected Browser $browser;

    protected Registry $registry;

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
