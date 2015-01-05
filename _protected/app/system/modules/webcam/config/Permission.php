<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2014-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Webcam / Config
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if (!AdminCore::auth()) // If the administrator is not logged
        {
            if (!$this->checkMembership() || !$this->group->webcam_access)
            {
                $this->paymentRedirect();
            }
        }
    }

}
