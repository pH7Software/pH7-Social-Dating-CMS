<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AccountController extends Controller
{
    const REDIRECTION_DELAY = 4; // In seconds

    /** @var string */
    private $sTitle;

    public function index()
    {
        $this->sTitle = t('Account - Affiliate');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->view->currency_sign = $this->config->values['module.setting']['currency_sign'];
        $this->view->min_withdrawal = $this->config->values['module.setting']['min_withdrawal_money'];
        $this->view->amount = (new AffiliateModel)->getAmount($this->session->get('affiliate_id'));
        $this->view->username = $this->session->get('affiliate_username');
        $this->view->contact_url = Uri::get('contact', 'contact', 'index');

        $this->output();
    }

    public function edit()
    {
        // Adding Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tabs.css');

        $this->sTitle = t('Edit your profile');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function password()
    {
        $this->sTitle = t('Change Password');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function delete()
    {
        $this->sTitle = t('Delete Affiliate Account');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->httpRequest->get('delete_status') === 'yesdelete') {
            $this->session->set('yes_delete', 1);
            Header::redirect(Uri::get('affiliate', 'account', 'yesdelete'));
        } elseif ($this->httpRequest->get('delete_status') === 'nodelete') {
            $this->view->delete_status = false;
            $this->design->setRedirect(
                Uri::get('affiliate', 'home', 'index'),
                null,
                null,
                self::REDIRECTION_DELAY
            );
        } else {
            $this->view->delete_status = true;
        }

        $this->output();
    }

    public function yesDelete()
    {
        if (!$this->session->exists('yes_delete')) {
            Header::redirect(Uri::get('affiliate', 'account', 'delete'));
        } else {
            $this->output();
        }
    }

    /**
     * @param string $sMail
     * @param string $sHash
     */
    public function activate($sMail, $sHash)
    {
        (new UserCore)->activateAccount(
            $sMail,
            $sHash,
            $this->config,
            $this->registry,
            'affiliate'
        );
    }
}
