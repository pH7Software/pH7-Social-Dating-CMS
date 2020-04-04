<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Registry\Registry;
use PH7\Framework\Security\Security;
use PH7\Framework\Session\Session;

// Automatic connection ('Remember Me' feature)
if (!UserCore::auth() && Registry::getInstance()->action !== 'soon') {
    $oCookie = new Cookie;
    if ($oCookie->exists(['member_remember', 'member_id'])) {
        if ((new ExistsCoreModel)->id($oCookie->get('member_id'))) {
            $oUserModel = new UserCoreModel;
            $oUser = $oUserModel->readProfile($oCookie->get('member_id'));
            if ($oCookie->get('member_remember') === Security::hashCookie($oUser->password)) {
                (new UserCore)->setAuth($oUser, $oUserModel, new Session, new SecurityModel);
            }
        }
    }
    unset($oCookie);
}
