<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2026, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Model\Engine\Util\Various;
use PH7\Framework\Mvc\Model\Security as SecurityModel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class VerificationCodeFormProcess extends Form
{
    /**
     * Every OPT is valid for 30 sec.
     *  * If somebody provides OTP at 29th sec, by the time it reaches the server OTP is expired.
     *  * So we can give OTP_TOLERANCE=1, it will check current & previous OTP.
     *  * OTP_TOLERANCE=2, verifies current and last two OTPS
     * - Text from: http://hayageek.com/two-factor-authentication-with-google-authenticator-php/.
     */
    private const OTP_TOLERANCE = 1;

    public function __construct(string $sMod)
    {
        parent::__construct();

        $iProfileId = TwoFactorAuthCore::getChallengeProfileId($this->session, $sMod);
        if ($iProfileId === null) {
            $this->rejectChallenge($sMod);

            return;
        }

        $oAuthenticator = TwoFactorAuthCore::createAuthenticator();
        $oSecurityModel = new SecurityModel();

        $oUserData = (new TwoFactorAuthModel($sMod))->getAuthProfile($iProfileId);
        if ($oUserData === false || (int)$oUserData->isTwoFactorAuth !== 1) {
            $this->rejectChallenge($sMod);

            return;
        }
        if (!is_string($oUserData->twoFactorAuthSecret)
            || !preg_match('/^[A-Z2-7]{16,40}$/D', $oUserData->twoFactorAuthSecret)) {
            $this->rejectChallenge($sMod, t('Two-factor authentication is unavailable for this account. Please contact the site administrator.'));

            return;
        }
        $sSecret = $oUserData->twoFactorAuthSecret;
        $sCode = $this->httpRequest->post('verification_code');

        /*
         * A 6-digit OTP can be brute-forced in minutes if attempts are unlimited,
         * so the same per-IP lockout used by the login forms applies here too.
         */
        if ($this->isLockedOut($sMod, $iProfileId, $oSecurityModel)) {
            \PFBC\Form::setError(
                'form_verification_code',
                Form::loginAttemptsExceededMsg($this->getAttemptTimeDelay($sMod))
            );

            return;
        }

        $bCheck = $oAuthenticator->verifyCode($sSecret, $sCode, self::OTP_TOLERANCE);

        if ($bCheck) {
            $oSecurityModel->clearLoginAttempts($this->getAttemptTable($sMod));
            $sCoreClassName = $this->getClassName($sMod);
            $sCoreModelClassName = $sCoreClassName . 'Model';
            $sCoreModelClass = new $sCoreModelClassName();
            $oCore = new $sCoreClassName();
            $mStatus = $sMod === PH7_ADMIN_MOD
                ? ((int)$oUserData->ban === UserCore::BAN_STATUS ? t('Sorry, Your account has been banned.') : true)
                : $oCore->checkAccountStatus($oUserData);
            if ($mStatus !== true) {
                $this->rejectChallenge($sMod, $mStatus);

                return;
            }

            if ($sMod === 'user') { // RememberMe is only available for "user" module
                $oRememberMe = new RememberMeCore();
                if ($oRememberMe->isEligible($this->session)) {
                    $oRememberMe->enableSession($oUserData);
                }
                unset($oRememberMe);
            }

            $oCore->setAuth(
                $oUserData,
                $sCoreModelClass,
                $this->session,
                new SecurityModel()
            );

            TwoFactorAuthCore::clearChallenge($this->session);
            $this->redirectToAccountPage($sMod);
        } else {
            $oSecurityModel->addLoginAttempt($this->getAttemptTable($sMod));
            \PFBC\Form::setError(
                'form_verification_code',
                t('Oops! The Verification Code is incorrect. Please try again.')
            );
        }
    }

    /**
     * Get main user core class according to the module.
     *
     * @param string $sMod module name
     *
     * @throws PH7InvalidArgumentException explanatory message if the specified module is wrong
     *
     * @return string correct class name
     */
    protected function getClassName(string $sMod): string
    {
        switch ($sMod) {
            case 'user':
                $sFullClassName = UserCore::class;
                break;
            case 'affiliate':
                $sFullClassName = AffiliateCore::class;
                break;
            case PH7_ADMIN_MOD:
                $sFullClassName = AdminCore::class;
                break;
            default:
                throw new PH7InvalidArgumentException(sprintf('Wrong "%s" module specified to get the class name', $sMod));
        }

        return $sFullClassName;
    }

    private function rejectChallenge(string $sMod, ?string $sMessage = null): void
    {
        TwoFactorAuthCore::clearChallenge($this->session);
        Header::redirect(
            TwoFactorAuthCore::getLoginUrl($sMod),
            $sMessage ?? t('Please sign in again. Your verification session is invalid or has expired.'),
            Design::ERROR_TYPE
        );
    }

    /**
     * Check the per-IP OTP attempt lockout, reusing the login-attempt settings of the module's role.
     */
    private function isLockedOut(string $sMod, $iProfileId, SecurityModel $oSecurityModel): bool
    {
        if (!(bool)DbConfig::getSetting('is' . $this->getSettingRole($sMod) . 'LoginAttempt')) {
            return false;
        }

        $iMaxAttempts = (int)DbConfig::getSetting('max' . $this->getSettingRole($sMod) . 'LoginAttempts');
        $sUserTable = Various::convertModToTable($sMod);
        $sEmail = (string)(new UserCoreModel())->getEmail((int)$iProfileId, $sUserTable);

        return !$oSecurityModel->checkLoginAttempt(
            $iMaxAttempts,
            $this->getAttemptTimeDelay($sMod),
            $sEmail,
            $this->view,
            $this->getAttemptTable($sMod),
            $sUserTable
        );
    }

    private function getAttemptTimeDelay(string $sMod): int
    {
        return (int)DbConfig::getSetting('login' . $this->getSettingRole($sMod) . 'AttemptTime');
    }

    private function getAttemptTable(string $sMod): string
    {
        switch ($sMod) {
            case 'affiliate':
                return DbTableName::AFFILIATE_ATTEMPT_LOGIN;
            case PH7_ADMIN_MOD:
                return DbTableName::ADMIN_ATTEMPT_LOGIN;
            default:
                return DbTableName::MEMBER_ATTEMPT_LOGIN;
        }
    }

    private function getSettingRole(string $sMod): string
    {
        switch ($sMod) {
            case 'affiliate':
                return 'Affiliate';
            case PH7_ADMIN_MOD:
                return 'Admin';
            default:
                return 'User';
        }
    }

    private function getAccountUrl(string $sModName): string
    {
        if ($sModName === PH7_ADMIN_MOD) {
            return Uri::get(PH7_ADMIN_MOD, 'main', 'index');
        }

        return Uri::get($sModName, 'account', 'index');
    }

    private function redirectToAccountPage(string $sMod): void
    {
        Header::redirect(
            $this->getAccountUrl($sMod),
            t('You are successfully logged in!')
        );
    }
}
