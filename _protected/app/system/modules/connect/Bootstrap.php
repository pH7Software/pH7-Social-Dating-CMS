<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect
 */
namespace PH7;
defined('PH7') or die('Restricted access');
use PH7\Framework\Config\Config;

// If the module is not enabled, we display a Not Found page, except if the administrator is logged, so it can make the module configuration.
if (!Config::getInstance()->values['module.setting']['enable'] && !AdminCore::auth())
    (new Controller)->displayPageNotFound();
