<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Datatype\Type;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;
use stdClass;

class UserController extends Controller implements UserModeratable
{
    use BulkAction;

    private const PROFILES_PER_PAGE = 15;
    private const SEARCH_NOT_FOUND_REDIRECT_DELAY = 2; // Seconds

    private UserCore $oUser;

    private AdminModel $oAdminModel;

    private ?string $sMsg;

    private int $iTotalUsers;

    public function __construct()
    {
        parent::__construct();

        $this->oUser = new UserCore;
        $this->oAdminModel = new AdminModel;

        // Assigns variables for views
        $this->view->designSecurity = new HtmlSecurity; // Security Design Class
        $this->view->dateTime = $this->dateTime; // Date Time Class
        $this->view->avatarDesign = new AvatarDesignCore; // For Avatar User
    }

    public function index(): void
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse')
        );
    }

    public function browse(): void
    {
        $this->iTotalUsers = $this->oAdminModel->total();

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages(
            $this->iTotalUsers,
            self::PROFILES_PER_PAGE
        );
        $this->view->current_page = $oPage->getCurrentPage();
        $oBrowse = $this->oAdminModel->browse(
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage()
        );
        unset($oPage);

        if (empty($oBrowse)) {
            $this->design->setRedirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse')
            );

            $this->displayPageNotFound(t('No user were found.'));
        } else {
            // Add the JS file for the browse form
            $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

            $this->view->page_title = $this->view->h1_title = t('Browse Users');
            $this->view->h3_title = t('Total Users: %0%', $this->iTotalUsers);
            $this->view->total_users = $this->iTotalUsers;
            $this->view->browse = $oBrowse;

            $this->output();
        }
    }

    public function add(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Add a User');
        $this->output();
    }

    public function import(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Import Users');
        $this->output();
    }

    public function addFakeProfiles(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Add Fake Profiles (with profile photo)');
        $this->output();
    }

    public function countryRestriction()
    {
        $this->view->page_title = $this->view->h1_title = t('Country Restrictions - User');
        $this->output();
    }

    public function search(): void
    {
        $this->view->page_title = $this->view->h1_title = t('User Search');
        $this->output();
    }

    public function result(): void
    {
        error_reporting(0);

        $iGroupId = $this->httpRequest->get('group_id', Type::INTEGER);
        $iBan = $this->httpRequest->get('ban', Type::INTEGER);
        $sWhere = $this->httpRequest->get('where');
        $sWhat = $this->httpRequest->get('what');

        if (!$this->areSearchArgsValid($sWhere)) {
            \PFBC\Form::setError('form_user_search', 'Invalid argument.');
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'search')
            );
        } else {
            $this->iTotalUsers = $this->oAdminModel->searchUser(
                $sWhat,
                $sWhere,
                $iGroupId,
                $iBan,
                true,
                $this->httpRequest->get('order'),
                $this->httpRequest->get('sort'),
                null,
                null
            );

            $oPage = new Page;
            $this->view->total_pages = $oPage->getTotalPages(
                $this->iTotalUsers,
                self::PROFILES_PER_PAGE
            );
            $this->view->current_page = $oPage->getCurrentPage();
            $oSearch = $this->oAdminModel->searchUser(
                $sWhat,
                $sWhere,
                $iGroupId,
                $iBan,
                false,
                $this->httpRequest->get('order'),
                $this->httpRequest->get('sort'),
                $oPage->getFirstItem(),
                $oPage->getNbItemsPerPage()
            );
            unset($oPage);

            if (empty($oSearch)) {
                $this->design->setRedirect(
                    Uri::get(PH7_ADMIN_MOD, 'user', 'search'),
                    null,
                    null,
                    self::SEARCH_NOT_FOUND_REDIRECT_DELAY
                );

                $this->displayPageNotFound(
                    t('No results found. Please try again with wider/new search criteria')
                );
            } else {
                // Add the JS file for the browse form
                $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

                $this->view->page_title = $this->view->h1_title = t('Users - Your search returned');
                $this->view->h3_title = nt('%n% user found!', '%n% users found!', $this->iTotalUsers);
                $this->view->browse = $oSearch;
            }

            $this->manualTplInclude('browse.tpl');
            $this->output();
        }
    }

    public function password(?string $sUserEmail = null): void
    {
        $bInvalidEmailId = empty($sUserEmail) || !(new Validate)->email($sUserEmail);
        if ($bInvalidEmailId) {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                t("The URL isn't valid. It doesn't contain the user's email as a parameter."),
                Design::ERROR_TYPE
            );
        } else {
            $this->view->page_title = $this->view->h1_title = t('Update User Password');
            $this->view->user_email = $sUserEmail;

            $this->output();
        }
    }

    public function loginUserAs($iId = null): void
    {
        if ($oUser = $this->oAdminModel->readProfile($iId)) {
            $aSessionData = [
                'login_user_as' => 1,
                'member_id' => $oUser->profileId,
                'member_email' => $oUser->email,
                'member_username' => $oUser->username,
                'member_first_name' => $oUser->firstName,
                'member_sex' => $oUser->sex,
                'member_group_id' => $oUser->groupId,
                'member_ip' => Ip::get(),
                'member_http_user_agent' => $this->browser->getUserAgent(),
                'member_token' => Various::genRnd($oUser->email)
            ];
            $this->session->set($aSessionData);
            $this->sMsg = t('You are now logged in as member: %0%!', $oUser->username);
            unset($oUser, $aSessionData);

            Header::redirect($this->registry->site_url, $this->sMsg);
        } else {
            Header::redirect(
                $this->httpRequest->previousPage(),
                t("This user doesn't exist."),
                Design::ERROR_TYPE
            );
        }
    }

    public function logoutUserAs(): void
    {
        $this->sMsg = t('You are now logged out as member: %0%!', $this->session->get('member_username'));

        $aSessionData = [
            'login_user_as',
            'member_id',
            'member_email',
            'member_username',
            'member_first_name',
            'member_sex',
            'member_group_id',
            'member_ip',
            'member_http_user_agent',
            'member_token'
        ];

        $this->session->remove($aSessionData);

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function approve(): void
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id', Type::INTEGER), 1)
        );
    }

    public function disapprove(): void
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id', Type::INTEGER), 0)
        );
    }

    public function approveAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('user_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 1);
            }
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function disapproveAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('user_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 0);
            }
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function ban(): void
    {
        $iId = $this->httpRequest->post('id');

        if ($this->oAdminModel->ban($iId, 1)) {
            $this->oUser->clearReadProfileCache($iId);
            $this->sMsg = t('The profile has been banned.');
        } else {
            $this->sMsg = t('Oops! An error has occurred while banishment the profile.');
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function unBan(): void
    {
        $iId = $this->httpRequest->post('id');

        if ($this->oAdminModel->ban($iId, 0)) {
            $this->oUser->clearReadProfileCache($iId);
            $this->sMsg = t('The profile has been unbanned.');
        } else {
            $this->sMsg = t('Oops! An error has occurred while unban the profile.');
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function delete(): void
    {
        try {
            $aData = explode('_', $this->httpRequest->post('id'));
            $iId = (int)$aData[0];
            $sUsername = (string)$aData[1];

            $this->oUser->delete($iId, $sUsername, new UserCoreModel);

            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                t('The profile has been deleted.')
            );
        } catch (ForbiddenActionException $oExcept) {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }

    public function banAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('user_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];

                $this->oAdminModel->ban($iId, 1);

                $this->oUser->clearReadProfileCache($iId);
            }
            $this->sMsg = t('The profile(s) has/have been banned.');
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function unBanAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('user_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];

                $this->oAdminModel->ban($iId, 0);
                $this->oUser->clearReadProfileCache($iId);
            }
            $this->sMsg = t('The profile(s) has/have been unbanned.');
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
            $this->sMsg
        );
    }

    public function deleteAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        try {
            if (!(new SecurityToken)->check('user_action')) {
                $this->sMsg = Form::errorTokenMsg();
            } elseif ($bActionsEligible) {
                $oUserModel = new UserCoreModel;

                foreach ($aActions as $sAction) {
                    $aData = explode('_', $sAction);
                    $iId = (int)$aData[0];
                    $sUsername = (string)$aData[1];

                    $this->oUser->delete($iId, $sUsername, $oUserModel);
                }
                unset($oUserModel);

                $this->sMsg = t('The profile(s) has/have been deleted.');
            }

            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                $this->sMsg
            );
        } catch (ForbiddenActionException $oExcept) {
            Header::redirect(
                Uri::get(PH7_ADMIN_MOD, 'user', 'browse'),
                $oExcept->getMessage(),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function moderateRegistration(int $iId, int $iStatus): string
    {
        if (isset($iId, $iStatus)) {
            if ($oUser = $this->oAdminModel->readProfile($iId)) {
                if ($iStatus === 0) {
                    // Set user not active
                    $this->oAdminModel->approve($oUser->profileId, 0);

                    // We leave the user in disapproval (but send an email). After we can ban or delete it
                    $sSubject = t('Your membership account has been declined');
                    $this->sMsg = t('Sorry, Your membership account has been declined.');
                } elseif ($iStatus === 1) {
                    // Approve user
                    $this->oAdminModel->approve($oUser->profileId, 1);

                    /** Update the Affiliate Commission **/
                    AffiliateCore::updateJoinCom($oUser->affiliatedId, $this->config, $this->registry);

                    $sSubject = t('Your membership account has been activated');
                    $this->sMsg = t('Congratulations! Your account has been approved by our team of administrators.<br />You can now %0% to meeting new people!',
                        '<a href="' . Uri::get('user', 'main', 'login') . '"><b>' . t('log in') . '</b></a>');
                } else {
                    // Error...
                    $this->sMsg = null;
                }

                if (!empty($sSubject) && !empty($this->sMsg)) {
                    $this->sendRegistrationMail($sSubject, $oUser);
                    $this->oUser->clearReadProfileCache($oUser->profileId);

                    $sOutputMsg = t('Done! ✔');
                } else {
                    $sOutputMsg = t('Error! Bad argument in the URL.');
                }
            } else {
                $sOutputMsg = t('The user is not found!');
            }
        } else {
            $sOutputMsg = t('Error! Missing argument in the URL.');
        }

        return $sOutputMsg;
    }

    private function sendRegistrationMail(string $sSubject, stdClass $oUser): void
    {
        $this->view->content = t('Hi %0%,', $oUser->firstName) . '<br />' . $this->sMsg;
        $this->view->footer = t('You are receiving this email because we received a registration application with "%0%" email address for %site_name% (%site_url%).', $oUser->email) . '<br />' .
            t('If you think someone has used your email address without your knowledge to create an account on %site_name%, please contact us using our contact form available on our website.');

        // Send email
        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/core/moderate_registration.tpl',
            $oUser->email
        );
        $aInfo = [
            'to' => $oUser->email,
            'subject' => $sSubject
        ];
        (new Mail)->send($aInfo, $sMessageHtml);
    }

    private function areSearchArgsValid(string $sWhere): bool
    {
        $aWhereOptions = [
            'all',
            SearchCoreModel::USERNAME,
            SearchCoreModel::EMAIL,
            SearchCoreModel::FIRST_NAME,
            SearchCoreModel::LAST_NAME,
            SearchCoreModel::IP
        ];

        return in_array($sWhere, $aWhereOptions, true);
    }
}
