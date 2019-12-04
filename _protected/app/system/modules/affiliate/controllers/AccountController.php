<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AccountController extends Controller
{
    const REDIRECTION_DELAY = 4; // In seconds

    public function index()
    {
        $this->setTitle(t('Account - Affiliate'));

        $this->view->currency_sign = $this->config->values['module.setting']['currency_sign'];
        $this->view->currency_code = $this->config->values['module.setting']['currency_code'];
        $this->view->min_withdrawal = $this->config->values['module.setting']['min_withdrawal_money'];
        $this->view->amount = (new AffiliateModel)->getAmount($this->session->get('affiliate_id'));
        $this->view->referral_link_url = Uri::get(
            'affiliate',
            'router',
            'refer',
            $this->session->get('affiliate_username')
        );
        $this->view->contact_url = Uri::get('contact', 'contact', 'index');

        $this->output();
    }

    public function edit()
    {
        // Adding Css Style for Tabs
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'tabs.css'
        );

        $this->setTitle(t('Edit your profile'));
        $this->output();
    }

    public function password()
    {
        $this->setTitle(t('Change Password'));
        $this->output();
    }

    public function delete()
    {
        $this->setTitle(t('Delete Affiliate Account'));

        if ($this->httpRequest->get('delete_status') === 'yesdelete') {
            $this->session->set('yes_delete', 1);
            Header::redirect(
                Uri::get(
                    'affiliate',
                    'account',
                    'yesdelete'
                )
            );
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
            Header::redirect(
                Uri::get(
                    'affiliate',
                    'account',
                    'delete'
                )
            );
        } else {
            $this->output();
        }
    }

    /**
     * @param string $sMail
     * @param string $sHash
     *
     * @throws Framework\File\IOException
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

    /**
     * Set title and heading.
     *
     * @param string $sTitle
     *
     * @return void
     */
    private function setTitle($sTitle)
    {
        $this->view->page_title = $this->view->h1_title = $sTitle;
    }
}
