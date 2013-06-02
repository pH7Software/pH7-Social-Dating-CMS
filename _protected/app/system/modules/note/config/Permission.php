<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');
use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\UriRoute;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        // Level for Notes

        if(!UserCore::auth() && ($this->registry->action === 'add' || $this->registry->action === 'edit' || $this->registry->action === 'delete'))
        {
            HeaderUrl::redirect(UriRoute::get('user','signup','step1'), t('Please register or login to add an article.'), 'error');
        }

        if(!AdminCore::auth() && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            HeaderUrl::redirect(UriRoute::get('blog','main','index'), $this->adminSignInMsg(), 'error');
        }
    }

}
