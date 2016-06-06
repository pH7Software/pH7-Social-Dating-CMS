<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User
 */
namespace PH7;
defined('PH7') or die('Restricted access');

// Automatic connection
if (!UserCore::auth() && Framework\Registry\Registry::getInstance()->action != 'soon')
{
    $oCookie = new Framework\Cookie\Cookie;
    if ($oCookie->exists(array('member_remember', 'member_id')))
    {
        if ((new ExistsCoreModel)->id($oCookie->get('member_id')))
        {
            $oUserModel = new UserCoreModel;
            $oUser = $oUserModel->readProfile($oCookie->get('member_id'));
            if ($oCookie->get('member_remember') === Framework\Security\Security::hashCookie($oUser->password))
            {
                (new UserCore)->setAuth($oUser, $oUserModel, new Framework\Session\Session, new Framework\Mvc\Model\Security);
            }
        }
    }
    unset($oCookie);
}
