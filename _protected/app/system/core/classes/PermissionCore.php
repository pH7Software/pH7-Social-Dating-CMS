<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

abstract class PermissionCore extends Framework\Core\Core
{

    protected $group;

    public function __construct()
    {
        parent::__construct();

        $this->group = UserCoreModel::checkGroup();
    }

    /**
     * Checks whether the user membership is still valid or not.
     *
     * @return boolean Returns TRUE if the membership is still valid (or user not logged), FALSE otherwise.
     */
    public function checkMembership()
    {
        return (UserCore::auth()) ? (new UserCoreModel)->checkMembershipExpiration($this->session->get('member_id'), $this->dateTime->get()->dateTime('Y-m-d H:i:s')) : true;
    }

    public function signUpRedirect()
    {
        Header::redirect(Uri::get('user','signup','step1'), $this->signUpMsg(), 'error');
    }

    public function signInRedirect()
    {
        Header::redirect(Uri::get('user','main','login'), $this->signInMsg(), 'error');
    }

    public function alreadyConnectedRedirect()
    {
        Header::redirect(Uri::get('user','account','index'), $this->alreadyConnectedMsg(), 'error');
    }

    /**
     * Redirect the user to the payment page when it is on a page that requires another membership.
     *
     * @return void
     */
    public function paymentRedirect()
    {
        Header::redirect(Uri::get('payment','main','index'), $this->upgradeMembershipMsg(), 'warning');
    }

    public function signInMsg()
    {
        return t('Please sign in first');
    }

    public function adminSignInMsg()
    {
        return t('Please go to the admin panel of the site and log in as administrator.');
    }

    public function alreadyConnectedMsg()
    {
        return t('Oops! You are already connected.');
    }

    public function signUpMsg()
    {
        return t('Please register or login to use this service.');
    }

    public function upgradeMembershipMsg()
    {
        return t('Please upgrade your membership!');
    }

}
