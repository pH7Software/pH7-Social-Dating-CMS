<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Picture / Config
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{
    public function __construct()
    {
        parent::__construct();

        if (!UserCore::auth() && ($this->registry->action === 'addalbum' || $this->registry->action === 'addphoto' ||
                $this->registry->action === 'editalbum' || $this->registry->action === 'editphoto' ||
                $this->registry->action === 'deletephoto' || $this->registry->action === 'deletealbum')
        ) {
            $this->signInRedirect();
        }

        if ($this->isNotAdmin()) {
            if (!$this->checkMembership() || !$this->group->view_pictures) {
                $this->paymentRedirect();
            } elseif (($this->registry->action === 'addalbum' || $this->registry->action === 'addphoto') &&
                !$this->group->upload_pictures) {
                $this->paymentRedirect();
            }
        }
    }
}
