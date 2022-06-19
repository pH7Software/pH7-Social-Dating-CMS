<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\Engine\Util\Various as VariousModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Generator\Password as PasswordGenerator;

class MainController extends Controller
{
    private const DEFAULT_PASSWORD_LENGTH = 8;

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

        $sTable = VariousModel::convertModToTable($sMod);

        if (!(new UserCoreModel)->checkHashValidation($sEmail, $sHash, $sTable)) {
            Header::redirect(
                $this->registry->site_url,
                t('Oops! Email or hash is invalid.'),
                Design::ERROR_TYPE
            );
        } else {
            if (!$this->sendMail($sTable, $sEmail)) {
                Header::redirect(
                    $this->registry->site_url,
                    Form::errorSendingEmail(),
                    Design::ERROR_TYPE
                );
            } else {
                Header::redirect(
                    $this->registry->site_url,
                    t('Your new password has been emailed to you.'),
                    Design::SUCCESS_TYPE
                );
            }
        }
    }

    public function account(): void
    {
        $sUrl = $this->getUserHomepageUrl();
        Header::redirect($sUrl);
    }

    /**
     * Send the new password by email.
     *
     * @param string $sTable DB table name.
     * @param string $sEmail The user email address.
     */
    protected function sendMail(string $sTable, string $sEmail): bool
    {
        // Get new password and change it in DB
        $sNewPassword = PasswordGenerator::generate(self::DEFAULT_PASSWORD_LENGTH);
        (new UserCoreModel)->changePassword($sEmail, $sNewPassword, $sTable);

        $this->view->content = t('Hello,') . '<br />' .
            t('Your new password is: %0%', '<em>' . $sNewPassword . '</em>') . '<br />' .
            t('Please change it once you are <a href="%0%">logged in</a> (Account -> Edit Profile -> Change Password).', $this->getLoginUrl($sTable));

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/lost-password/recover_password.tpl',
            $sEmail
        );

        $aInfo = [
            'to' => $sEmail,
            'subject' => t('Your new password - %site_name%')
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    private function getLoginUrl(string $sTableName): string
    {
        switch ($sTableName) {
            case DbTableName::MEMBER:
                return Uri::get('user', 'main', 'index');

            case DbTableName::AFFILIATE:
                return Uri::get('affiliate', 'home', 'login');

            case DbTableName::ADMIN:
                return Uri::get(PH7_ADMIN_MOD, 'main', 'login');
        }
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
