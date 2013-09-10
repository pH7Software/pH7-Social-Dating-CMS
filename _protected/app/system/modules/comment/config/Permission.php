<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Comment / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if ((!UserCore::auth() && !AdminCore::auth()) && ($this->registry->action === 'add' || $this->registry->action === 'delete'))
        {
            Framework\Url\HeaderUrl::redirect(Uri::get('user','main','login'), $this->signInMsg(), 'error');
        }

    }

}
