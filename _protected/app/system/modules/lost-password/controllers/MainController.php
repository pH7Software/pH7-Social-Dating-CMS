<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Html\Meta;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\Engine\Util\Various as VariousModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use PH7\Framework\Util\Various;

class MainController extends Controller
{
    const DEFAULT_PASSWORD_LENGTH = 8;

    /**
     * @param string $sMod
     *
     * @return void
     */
    public function forgot($sMod = '')
    {
        // For better SEO, exclude not interesting pages from search engines
        $this->view->header = Meta::NOINDEX;

        $this->checkMod($sMod);

        $this->view->page_title = t('Forgot your Password?');
        $this->view->h1_title = t('Password Reset');
        $this->output();
    }

    /**
     * @param string $sMod
     * @param string $sEmail
     * @param string $sHash
     *
     * @return void
     */
    public function reset($sMod = '', $sEmail = '', $sHash = '')
    {
        $this->checkMod($sMod);

        $sTable = VariousModel::convertModToTable($sMod);

        if (!(new UserCoreModel)->checkHashValidation($sEmail, $sHash, $sTable)) {
            Header::redirect($this->registry->site_url, t('Oops! Email or hash is invalid.'), Design::ERROR_TYPE);
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

    /**
     * @return void
     */
    public function account()
    {
        $sUrl = $this->getUserHomepageUrl();
        Header::redirect($sUrl);
    }

    /**
     * Send the new password by email.
     *
     * @param string $sTable DB table name.
     * @param string $sEmail The user email address.
     *
     * @return integer Number of recipients who were accepted for delivery.
     */
    protected function sendMail($sTable, $sEmail)
    {
        // Get new password and change it in DB
        $sNewPassword = Various::genRndWord(self::DEFAULT_PASSWORD_LENGTH);
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

    /**
     * @param string $sTableName
     *
     * @return string
     */
    private function getLoginUrl($sTableName)
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

    /**
     * @return string
     */
    private function getUserHomepageUrl()
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

    /**
     * @param string $sMod
     *
     * @return void
     */
    private function checkMod($sMod)
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
