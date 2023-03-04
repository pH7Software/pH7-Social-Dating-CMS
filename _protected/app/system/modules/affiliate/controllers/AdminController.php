<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
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
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;
use stdClass;

class AdminController extends Controller implements UserModeratable
{
    use BulkAction;

    private const PROFILES_PER_PAGE = 15;
    private const REDIRECTION_DELAY_IN_SEC = 5;

    private Affiliate $oAff;

    private AffiliateModel $oAffModel;

    private ?string $sMsg;

    private string $sTitle;

    private int $iTotalUsers;

    public function __construct()
    {
        parent::__construct();

        $this->oAff = new Affiliate;
        $this->oAffModel = new AffiliateModel;
    }

    public function index(): void
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            t('Welcome to the administration of Ad Affiliate')
        );
    }

    public function config(): void
    {
        $this->sTitle = t('Affiliate Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function banner(): void
    {
        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'setting',
                'ads',
                'affiliate'
            )
        );
    }

    public function countryRestriction(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Country Restrictions - Affiliate');
        $this->output();
    }

    public function browse(): void
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $this->iTotalUsers = $this->oAffModel->searchAff(
            $sKeywords,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages(
            $this->iTotalUsers,
            self::PROFILES_PER_PAGE
        );
        $this->view->current_page = $oPage->getCurrentPage();
        $oSearch = $this->oAffModel->searchAff(
            $sKeywords,
            false,
            $sOrder,
            $iSort,
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage()
        );
        unset($oPage);

        if (empty($oSearch)) {
            $this->setNotFoundPage();
        } else {
            // Add the js file necessary for the browse form
            $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

            // Assigns variables for views
            $this->view->designSecurity = new HtmlSecurity; // Security Design Class
            $this->view->dateTime = $this->dateTime; // Date Time Class

            $this->sTitle = t('Browse Affiliates');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Affiliate', '%n% Affiliates', $this->iTotalUsers);

            $this->view->browse = $oSearch;
        }

        $this->output();
    }

    public function search(): void
    {
        $this->sTitle = t('Affiliate Search');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function add(): void
    {
        $this->sTitle = t('Add an Affiliate');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function loginUserAs($iId = null): void
    {
        if ($oUser = $this->oAffModel->readProfile($iId, DbTableName::AFFILIATE)) {
            $aSessionData = [
                'login_affiliate_as' => 1,
                'affiliate_id' => $oUser->profileId,
                'affiliate_email' => $oUser->email,
                'affiliate_username' => $oUser->username,
                'affiliate_first_name' => $oUser->firstName,
                'affiliate_sex' => $oUser->sex,
                'affiliate_ip' => Ip::get(),
                'affiliate_http_user_agent' => $this->browser->getUserAgent(),
                'affiliate_token' => Various::genRnd($oUser->email)
            ];
            $this->session->set($aSessionData);
            $this->sMsg = t('You are now logged in as affiliate: %0%!', $oUser->username);
            unset($oUser, $aSessionData);

            Header::redirect(
                Uri::get('affiliate', 'account', 'index'),
                $this->sMsg
            );
        } else {
            Header::redirect(
                $this->httpRequest->previousPage(),
                t("This affiliate doesn't exist."),
                Design::ERROR_TYPE
            );
        }
    }

    public function logoutUserAs(): void
    {
        $this->sMsg = t('You are now logged out as affiliate: %0%!', $this->session->get('affiliate_username'));

        $aSessionData = [
            'login_affiliate_as',
            'affiliate_id',
            'affiliate_email',
            'affiliate_username',
            'affiliate_first_name',
            'affiliate_sex',
            'affiliate_ip',
            'affiliate_http_user_agent',
            'affiliate_token'
        ];

        $this->session->remove($aSessionData);

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function approve(): void
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id', Type::INTEGER), 1)
        );
    }

    public function disapprove(): void
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id', Type::INTEGER), 0)
        );
    }

    public function approveAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 1);
            }
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function disapproveAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 0);
            }
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function ban(): void
    {
        $iId = $this->httpRequest->post('id');

        if ($this->oAffModel->ban($iId, 1, DbTableName::AFFILIATE)) {
            $this->oAff->clearReadProfileCache($iId, DbTableName::AFFILIATE);
            $this->sMsg = t('The affiliate has been banned.');
        } else {
            $this->sMsg = t('Oops! An error has occurred while banishment the affiliate.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function unBan(): void
    {
        $iId = $this->httpRequest->post('id');

        if ($this->oAffModel->ban($iId, 0, DbTableName::AFFILIATE)) {
            $this->oAff->clearReadProfileCache($iId, DbTableName::AFFILIATE);
            $this->sMsg = t('The affiliate has been unbanned.');
        } else {
            $this->sMsg = t('Oops! An error has occurred while unban the affiliate.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function delete(): void
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iId = (int)$aData[0];
        $sUsername = (string)$aData[1];

        $this->oAff->delete($iId, $sUsername, $this->oAffModel);
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            t('The affiliate has been deleted.')
        );
    }

    public function banAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];

                $this->oAffModel->ban($iId, 1, DbTableName::AFFILIATE);
                $this->oAff->clearReadProfileCache($iId, DbTableName::AFFILIATE);
            }
            $this->sMsg = t('The affiliate(s) has/have been banned.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function unBanAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $iId = (int)explode('_', $sAction)[0];

                $this->oAffModel->ban($iId, 0, DbTableName::AFFILIATE);
                $this->oAff->clearReadProfileCache($iId, DbTableName::AFFILIATE);
            }
            $this->sMsg = t('The affiliate(s) has/have been unbanned.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function deleteAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $sAction) {
                $aData = explode('_', $sAction);
                $iId = (int)$aData[0];
                $sUsername = (string)$aData[1];

                $this->oAff->delete($iId, $sUsername, $this->oAffModel);
            }
            $this->sMsg = t('The affiliate(s) has/have been deleted.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    /**
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function moderateRegistration(int $iId, int $iStatus): string
    {
        if (isset($iId, $iStatus)) {
            if ($oUser = $this->oAffModel->readProfile($iId, DbTableName::AFFILIATE)) {
                if ($iStatus === 0) {
                    // Set user not active
                    $this->oAffModel->approve($oUser->profileId, 0, DbTableName::AFFILIATE);

                    // We leave the user in disapproval (but send an email). After we can ban or delete it.
                    $sSubject = t('Your membership account has been declined');
                    $this->sMsg = t('Sorry, your affiliate application has been declined.');
                } elseif ($iStatus === 1) {
                    // Approve user
                    $this->oAffModel->approve($oUser->profileId, 1, DbTableName::AFFILIATE);

                    /** Update the Affiliate Commission **/
                    AffiliateCore::updateJoinCom($oUser->affiliatedId, $this->config, $this->registry);

                    $sSubject = t('Your membership account has been activated');
                    $this->sMsg = t('Congratulations! Your account has been approved by %site_name% team.<br />You can now %0% and start making money by promotioning the website!',
                        '<a href="' . Uri::get('affiliate', 'home', 'login') . '"><b>' . t('log in') .
                        '</b></a>');
                } else {
                    // Error...
                    $this->sMsg = null;
                }

                if (!empty($sSubject) && !empty($this->sMsg)) {
                    $this->sendRegistrationMail($sSubject, $oUser);
                    $this->oAff->clearReadProfileCache($oUser->profileId, DbTableName::AFFILIATE);

                    $sOutputMsg = t('Done! ✔');
                } else {
                    $sOutputMsg = t('Error! Bad argument in the URL.');
                }
            } else {
                $sOutputMsg = t("The requested user ID wasn't found.");
            }
        } else {
            $sOutputMsg = t('Error! Missing argument in the URL.');
        }

        return $sOutputMsg;
    }

    /**
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendRegistrationMail(string $sSubject, stdClass $oUser): void
    {
        $this->view->content = t('Dear %0%,', $oUser->firstName) . '<br />' . $this->sMsg;
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

    /**
     * Redirect to admin browse page, then display the default "Not Found" page.
     */
    private function setNotFoundPage(): void
    {
        $this->design->setRedirect(
            Uri::get(
                'affiliate',
                'admin',
                'browse'
            ),
            null,
            null,
            self::REDIRECTION_DELAY_IN_SEC
        );

        $sErrorMsg = t('No affiliates have been found.');
        $this->displayPageNotFound($sErrorMsg);
    }
}
