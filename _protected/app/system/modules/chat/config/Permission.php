<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Chat / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');
use PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        /***** Levels for members *****/
        // Options and Memberships ...
        /*
        if(!UserCore::auth() && (!$this->group->can_chat)) {
            Framework\Url\HeaderUrl::redirect(Uri::get('user','main','login'), $this->alreadyConnectedMsg(), 'error');
        } elseif(!$this->group->can_chat) {
            $this->design->setFlash(t('No access'));
            return;
        }
        */

        /*
        if(!UserCore::auth()) {
            Framework\Url\HeaderUrl::redirect(Uri::get('user','signup','step1'), t('You need to register for free to use the Free Chat Rooms!'));
        }
        */
    }

}
