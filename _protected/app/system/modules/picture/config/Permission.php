<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Picture / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');
use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if(!UserCore::auth() && ($this->registry->action === 'addphoto' || $this->registry->action === 'addalbum'
        || $this->registry->action === 'editalbum' || $this->registry->action === 'editphoto'
        || $this->registry->action === 'deletephoto' || $this->registry->action === 'deletealbum'))
        {
            HeaderUrl::redirect(Uri::get('user','main','login'), $this->signInMsg(), 'error');
        }
    }

}
