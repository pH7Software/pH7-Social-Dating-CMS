<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2013-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Field / Config
 */
namespace PH7;
defined('PH7') or die('Restricted access');

class Permission extends PermissionCore
{

    public function __construct()
    {
        parent::__construct();

        if (!AdminCore::auth())
        {
             // For security reasons, we do not redirectionnons the user to hide the url of the administrative part.
            Framework\Url\Header::redirect(PH7_URL_ROOT, $this->adminSignInMsg(), 'error');
        }
    }

}
