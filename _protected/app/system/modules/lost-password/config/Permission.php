<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        // Admin Security, if you have forgotten your admin password, comment this code below
        if ($this->httpRequest->get('mod') == PH7_ADMIN_MOD && ($this->registry->action == 'forgot' || $this->registry->action == 'reset'))
        {
            Header::redirect(Uri::get(PH7_ADMIN_MOD,'main','login'), t('For security reasons, you do not have the right to generate a new password. To disable this security option, you must go to the Permission file of "lost-password" module'), 'error');
        }

        if ((UserCore::auth() || AffiliateCore::auth() || AdminCore::auth()) && ($this->registry->action == 'forgot' || $this->registry->action == 'reset'))
        {
            Header::redirect(Uri::get('lost-password', 'main', 'account'), $this->alreadyConnectedMsg(), 'error');
        }
    }

}
