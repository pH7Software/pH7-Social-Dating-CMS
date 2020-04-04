<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

use PH7\Framework\Ip\Ip;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Security as HtmlSecurity;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class AdminController extends Controller
{
    const PROFILES_PER_PAGE = 15;
    const REDIRECTION_DELAY_IN_SEC = 5;

    /** @var Affiliate */
    private $oAff;

    /** @var AffiliateModel */
    private $oAffModel;

    /** @var string */
    private $sMsg;

    /** @var string */
    private $sTitle;

    /** @var int */
    private $iTotalUsers;

    public function __construct()
    {
        parent::__construct();

        $this->oAff = new Affiliate;
        $this->oAffModel = new AffiliateModel;
    }

    public function index()
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            t('Welcome to the administration of Ad Affiliate')
        );
    }

    public function config()
    {
        $this->sTitle = t('Affiliate Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function banner()
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

    public function countryRestriction()
    {
        $this->view->page_title = $this->view->h1_title = t('Country Restrictions - Affiliate');
        $this->output();
    }

    public function browse()
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

    public function search()
    {
        $this->sTitle = t('Affiliate Search');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function add()
    {
        $this->sTitle = t('Add an Affiliate');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function loginUserAs($iId = null)
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

    public function logoutUserAs()
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

    public function approve()
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id'), 1)
        );
    }

    public function disapprove()
    {
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->moderateRegistration($this->httpRequest->post('id'), 0)
        );
    }

    public function approveAll()
    {
        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 1);
            }
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function disapproveAll()
    {
        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sAction) {
                $iId = (int)explode('_', $sAction)[0];
                $this->sMsg = $this->moderateRegistration($iId, 0);
            }
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    public function ban()
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

    public function unBan()
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

    public function delete()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iId = (int)$aData[0];
        $sUsername = (string)$aData[1];

        $this->oAff->delete($iId, $sUsername);
        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            t('The affiliate has been deleted.')
        );
    }

    public function banAll()
    {
        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sAction) {
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

    public function unBanAll()
    {
        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sAction) {
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

    public function deleteAll()
    {
        if (!(new SecurityToken)->check('aff_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sAction) {
                $aData = explode('_', $sAction);
                $iId = (int)$aData[0];
                $sUsername = (string)$aData[1];

                $this->oAff->delete($iId, $sUsername);
            }
            $this->sMsg = t('The affiliate(s) has/have been deleted.');
        }

        Header::redirect(
            Uri::get('affiliate', 'admin', 'browse'),
            $this->sMsg
        );
    }

    /**
     * @param int $iId
     * @param int $iStatus
     *
     * @return string Status message.
     *
     * @throws Framework\File\IOException
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function moderateRegistration($iId, $iStatus)
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

                if (!empty($this->sMsg)) {
                    $this->sendRegistrationMail($sSubject, $oUser);
                    $this->oAff->clearReadProfileCache($oUser->profileId, DbTableName::AFFILIATE);

                    $sOutputMsg = t('Done!');
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
     * @param string $sSubject
     * @param stdClass $oUser
     *
     * @return void
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendRegistrationMail($sSubject, stdClass $oUser)
    {
        // Set body messages + footer
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
     *
     * @return void
     */
    private function setNotFoundPage()
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
