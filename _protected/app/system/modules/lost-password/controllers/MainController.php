<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Mvc\Model\Engine\Util\Various as VariousModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class MainController extends Controller
{
    public function forgot(string $sMod = ''): void
    {
        // For better SEO, exclude not interesting pages from search engines
        $this->view->header = Meta::NOINDEX;

        $this->checkMod($sMod);

        $this->view->page_title = t('Forgot your Password?');
        $this->view->h1_title = t('Password Reset');
        $this->output();
    }

    public function reset(string $sMod = '', string $sEmail = '', string $sHash = ''): void
    {
        $this->checkMod($sMod);
        header('Cache-Control: no-store');
        header('Referrer-Policy: no-referrer');
        $sTable = VariousModel::convertModToTable($sMod);

        if (!(new PasswordResetModel())->isValid($sEmail, $sHash, $sTable)) {
            Header::redirect(
                Uri::get('lost-password', 'main', 'forgot', $sMod),
                t('This password reset link is invalid or expired. Please request a new one.'),
                Design::ERROR_TYPE
            );
        }

        $this->view->header = Meta::NOINDEX;
        $this->view->page_title = t('Choose a new password');
        $this->view->h1_title = t('Password Reset');
        $this->view->reset_mod = $sMod;
        $this->view->reset_email = $sEmail;
        $this->view->reset_token = $sHash;
        $this->output();
    }

    public function account(): void
    {
        $sUrl = $this->getUserHomepageUrl();
        Header::redirect($sUrl);
    }

    private function getUserHomepageUrl(): string
    {
        if (UserCore::auth()) {
            $sUrl = Uri::get('user', 'account', 'index');
        } elseif (AffiliateCore::auth()) {
            $sUrl = Uri::get('affiliate', 'account', 'index');
        } elseif (AdminCore::auth()) {
            $sUrl = Uri::get(PH7_ADMIN_MOD, 'main', 'index');
        } else {
            $sUrl = $this->registry->site_url;
        }

        return $sUrl;
    }

    private function checkMod(string $sMod): void
    {
        $aMods = ['user', 'affiliate', PH7_ADMIN_MOD];

        if (!in_array($sMod, $aMods, true)) {
            Header::redirect(
                $this->registry->site_url,
                t('Module not found!'),
                Design::ERROR_TYPE
            );
        }
    }
}
