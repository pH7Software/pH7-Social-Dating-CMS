<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');
use PH7\Framework\Mvc\Router\UriRoute, PH7\Framework\Url\HeaderUrl;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        // This module is available only to members
        if(!UserCore::auth())
            HeaderUrl::redirect(UriRoute::get('user', 'signup', 'step1'), t('You must register to report the abuse.'));
    }

}
