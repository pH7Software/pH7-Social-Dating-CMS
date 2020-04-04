<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

abstract class PermissionCore extends Framework\Core\Core
{
    const VISITOR_GROUP_ID = 1;

    /** @var UserCoreModel */
    private $oUserModel;

    /** @var \stdClass */
    protected $group;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserCoreModel;
        $this->group = $this->oUserModel->checkGroup($this->session);
    }

    /**
     * Checks whether the user membership is still valid or not.
     *
     * @return bool Returns TRUE if the membership is still valid (or user not logged), FALSE otherwise.
     */
    public function checkMembership()
    {
        if (UserCore::auth()) {
            return $this->oUserModel->checkMembershipExpiration(
                $this->session->get('member_id'),
                $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
            );
        }

        return true;
    }

    public function signUpRedirect()
    {
        Header::redirect(
            Uri::get('user', 'signup', 'step1'),
            $this->signUpMsg(),
            Design::ERROR_TYPE
        );
    }

    /**
     * @return string
     */
    public function signUpMsg()
    {
        return t('Please register or login to continue.');
    }

    public function signInRedirect()
    {
        Header::redirect(
            Uri::get('user', 'main', 'login'),
            $this->signInMsg(),
            Design::ERROR_TYPE
        );
    }

    /**
     * @return string
     */
    public function signInMsg()
    {
        return t('Please sign in first 😉');
    }

    public function alreadyConnectedRedirect()
    {
        Header::redirect(
            Uri::get('user', 'account', 'index'),
            $this->alreadyConnectedMsg(),
            Design::ERROR_TYPE
        );
    }

    /**
     * @return string
     */
    public function alreadyConnectedMsg()
    {
        return t('Oops! You are already connected.');
    }

    /**
     * Redirect the user to the payment page when it is on a page that requires another membership.
     *
     * @return void
     */
    public function paymentRedirect()
    {
        Header::redirect(
            Uri::get('payment', 'main', 'index'),
            $this->upgradeMembershipMsg(),
            Design::WARNING_TYPE
        );
    }

    /**
     * @return string
     */
    public function upgradeMembershipMsg()
    {
        return t('Please upgrade your membership!');
    }

    /**
     * @return string
     */
    public function adminSignInMsg()
    {
        return t('Please go to the admin panel and log in as administrator.');
    }
}
