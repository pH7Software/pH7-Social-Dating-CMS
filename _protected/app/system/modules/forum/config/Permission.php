<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Config
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        /***** Levels for the forums *****/
        $bAdminAuth = AdminCore::auth();

        if ((!UserCore::auth() && !$bAdminAuth) && ($this->registry->action === 'addtopic' || $this->registry->action === 'edittopic'
                || $this->registry->action === 'deletetopic' || $this->registry->action === 'reply' || $this->registry->action === 'editmessage'
                || $this->registry->action === 'deletemessage')) {
            $this->signInRedirect();
        }

        if (!$bAdminAuth || UserCore::isAdminLoggedAs()) {
            if (!$this->checkMembership() || !$this->group->forum_access) {
                $this->paymentRedirect();
            } elseif ($this->registry->action === 'addtopic' && !$this->group->create_forum_topics) {
                $this->paymentRedirect();
            } elseif ($this->registry->action === 'reply' && !$this->group->answer_forum_topics) {
                $this->paymentRedirect();
            }
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController') {
            // For security reasons, we don't redirect the user to the admin panel URL
            Header::redirect(
                Uri::get('forum', 'forum', 'index'),
                $this->adminSignInMsg(),
                Design::ERROR_TYPE
            );
        }
    }
}
