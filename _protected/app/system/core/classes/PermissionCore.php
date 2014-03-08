<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2014, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

abstract class PermissionCore extends Framework\Core\Core
{

    protected $group;

    public function __construct()
    {
        parent::__construct();

        $this->group = UserCoreModel::checkGroup();
    }

    /**
     * Checks whether the user membership is still valid.
     *
     * @return void
     */
    public function checkMembership()
    {
        return (UserCore::auth()) ? (new UserCoreModel)->checkMembershipExpiration($this->session->get('member_id'), $this->dateTime->get()->dateTime('Y-m-d H:i:s')) : true;
    }

    public function signInMsg()
    {
        return t('Please you logged!');
    }

    public function adminSignInMsg()
    {
        return t('Please go to the administrative part of the site and log in as administrator.');
    }

    public function alreadyConnectedMsg()
    {
        return t('Oops! You are already connected.');
    }

    public function signUpMsg()
    {
        return t('Please register or login to use this service.');
    }

}
