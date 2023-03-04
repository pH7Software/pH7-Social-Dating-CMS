<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AccountController extends Controller
{
    private const REDIRECTION_DELAY = 4; // In seconds

    public function index(): void
    {
        $this->setTitle(t('Account - Affiliate'));

        $sReferralUrl = $this->getReferralLink();

        $this->view->currency_sign = $this->config->values['module.setting']['currency_sign'];
        $this->view->currency_code = $this->config->values['module.setting']['currency_code'];
        $this->view->min_withdrawal = $this->config->values['module.setting']['min_withdrawal_money'];
        $this->view->amount = (new AffiliateModel)->getAmount($this->session->get('affiliate_id'));
        $this->view->referral_link_url = $sReferralUrl;
        $this->view->tweet_msg_url = SocialSharing::getTwitterLink(
            t("Let's have fun! 😻 Let's try something different 😍\n-> %0% 🥳", $sReferralUrl)
        );
        $this->view->contact_url = Uri::get('contact', 'contact', 'index');

        $this->output();
    }

    public function edit(): void
    {
        // Adding Css Style for Tabs
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'tabs.css'
        );

        $this->setTitle(t('Edit your profile'));
        $this->output();
    }

    public function password(): void
    {
        $this->setTitle(t('Change Password'));
        $this->output();
    }

    public function delete(): void
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

    public function yesDelete(): void
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
    public function activate($sMail, $sHash): void
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
     * Give a referral affiliation link.
     */
    private function getReferralLink(): string
    {
        return Uri::get(
            'affiliate',
            'router',
            'refer',
            $this->session->get('affiliate_username')
        );
    }

    /**
     * Set title and heading.
     */
    private function setTitle(string $sTitle): void
    {
        $this->view->page_title = $this->view->h1_title = $sTitle;
    }
}
