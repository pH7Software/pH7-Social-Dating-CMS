<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        /***** Levels for the forums *****/
        $bAdminAuth = AdminCore::auth();

        if ((!UserCore::auth() && !$bAdminAuth) && ($this->registry->action === 'addtopic' || $this->registry->action === 'edittopic'
        || $this->registry->action === 'deletetopic' || $this->registry->action === 'reply' || $this->registry->action === 'editmessage'
        || $this->registry->action === 'deletemessage'))
        {
            $this->signInRedirect();
        }

        if (!$bAdminAuth || UserCore::isAdminLoggedAs())
        {
            if (!$this->checkMembership() || !$this->group->forum_access)
            {
                $this->paymentRedirect();
            }
            elseif ($this->registry->action === 'addtopic' && !$this->group->create_forum_topics)
            {
                $this->paymentRedirect();
            }
            elseif ($this->registry->action === 'reply' && !$this->group->answer_forum_topics)
            {
                $this->paymentRedirect();
            }
        }

        if (!$bAdminAuth && $this->registry->controller === 'AdminController')
        {
            // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('forum','forum','index'), $this->adminSignInMsg(), 'error');
        }
    }

}
