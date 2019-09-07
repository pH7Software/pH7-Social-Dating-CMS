<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Controller
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Parse\Url;
use PH7\Framework\Url\Header;
use RobThree\Auth\TwoFactorAuth as Authenticator;

class MainController extends Controller
{
    const AUTHENTICATOR_IOS_APP_URL = 'https://itunes.apple.com/en/app/google-authenticator/id388497605';
    const AUTHENTICATOR_ANDROID_APP_URL = 'https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2';

    const TWO_FACTOR_SECRET_STRING_LENGTH = 10;
    const WRONG_MODULE_ERROR_MESSAGE = 'Wrong "%s" module!';
    const BACKUP_CODE_FILE_EXT = '.txt';

    /** @var TwoFactorAuthModel */
    private $o2FactorModel;

    /** @var Authenticator */
    private $oAuthenticator;

    /** @var string */
    private $sMod;

    /** @var int */
    private $iIsEnabled;

    /** @var int */
    private $iProfileId;

    public function __construct()
    {
        parent::__construct();

        $this->oAuthenticator = new Authenticator($this->registry->site_url);

    }

    public function verificationCode($sMod = '')
    {
        $this->sMod = $sMod;
        $this->checkMod();

        $this->view->page_title = $this->view->h2_title = t('Verification Code - Two-Factor Authentication');
        $this->output();
    }

    public function setup($sMod = '')
    {
        $this->sMod = $sMod;
        $this->checkMod();

        $this->iProfileId = $this->getProfileId();
        $this->o2FactorModel = new TwoFactorAuthModel($this->sMod);

        $this->view->page_title = $this->view->h2_title = t('Two-Factor Authentication');
        $this->view->mod = $this->sMod;

        $this->iIsEnabled = (int)$this->o2FactorModel->isEnabled($this->iProfileId);
        if ($this->httpRequest->postExists('status')) {
            $this->update2FaStatus();
        }
        // Assign to the template after "update2FaStatus()" to get the accurate status in case it has been updated just now
        $this->view->is_enabled = $this->iIsEnabled;

        $sSecret = $this->o2FactorModel->getSecret($this->iProfileId);

        // If not setup yet, create a new 2FA secret code for the profile
        if (!$this->isTwoFactorSet($sSecret)) {
            $sSecret = $this->oAuthenticator->createSecret();
            $this->o2FactorModel->setSecret($sSecret, $this->iProfileId);
        }

        if ($this->httpRequest->postExists('get_backup_code')) {
            $this->download($sSecret);
            exit;
        }

        $this->view->authenticator_ios_app_url = self::AUTHENTICATOR_IOS_APP_URL;
        $this->view->authenticator_android_app_url = self::AUTHENTICATOR_ANDROID_APP_URL;
        $this->view->qr_core = $this->oAuthenticator->getQRCodeImageAsDataUri($this->getAuthenticatorName(), $sSecret, 240);

        $this->output();
    }

    /**
     * Download the backup 2FA code (text file).
     *
     * @param string $sSecret The 2FA secret.
     *
     * @return void
     */
    private function download($sSecret)
    {
        $sFileName = '2FA-backup-code-' . $this->sMod . '-' . Url::clean($this->registry->site_name) . self::BACKUP_CODE_FILE_EXT;
        header('Content-Disposition: attachment; filename=' . $sFileName);
        $sBackupCodeTextMessage = $this->getBackupCodeMessage($sSecret);

        echo $sBackupCodeTextMessage;
    }

    /**
     * @param string $sSecret The 2FA secret code.
     *
     * @return string
     */
    private function getBackupCodeMessage($sSecret)
    {
        $sTxtMsg = t('BACKUP VERIFICATION CODE - %site_url% | %0%', $this->sMod) . "\r\n\r\n";
        $sTxtMsg .= t('Code: %0%', $this->oAuthenticator->getCode($sSecret)) . "\r\n\r\n";
        $sTxtMsg .= t('Generated on: %0%', $this->dateTime->get()->date()) . "\r\n\r\n";
        $sTxtMsg .= t('Print it and keep it in a safe place, like your wallet.') . "\r\n\r\n\r\n";
        $sTxtMsg .= t('Regards, %site_name%') . "\r\n";
        $sTxtMsg .= '-----' . "\r\n";
        $sTxtMsg .= t('Powered by "pH7CMS.com" software.') . "\r\n";

        return $sTxtMsg;
    }

    /**
     * Get Session Profile ID.
     *
     * @return integer
     *
     * @throws PH7InvalidArgumentException Explanatory message if the specified module is wrong.
     */
    private function getProfileId()
    {
        switch ($this->sMod) {
            case 'user':
                return $this->session->get('member_id');
            case 'affiliate':
                return $this->session->get('affiliate_id');
            case PH7_ADMIN_MOD:
                return $this->session->get('admin_id');

            default:
                throw new PH7InvalidArgumentException(
                    sprintf(self::WRONG_MODULE_ERROR_MESSAGE, $this->sMod)
                );
        }
    }

    /**
     * Turn on/off Two-Factor authentication.
     *
     * @return void
     */
    private function update2FaStatus()
    {
        $this->iIsEnabled = ($this->iIsEnabled === 1) ? 0 : 1; // Get the opposite value (if 1 so 0 | if 0 so 1)

        $this->o2FactorModel->setStatus($this->iIsEnabled, $this->iProfileId);
    }

    /**
     * Generate an Authenticator Name for the QR code.
     * Note: I don't use the site name because it might include invalid characters.
     *
     * @return string Unique Authenticator Name for the site.
     */
    private function getAuthenticatorName()
    {
        return str_replace('/', '-', Url::name($this->registry->site_url)) . '-' . $this->sMod;
    }

    /**
     * @param string $sSecret The 2FA secret code.
     *
     * @return bool
     */
    private function isTwoFactorSet($sSecret)
    {
        return !empty($sSecret) && strlen($sSecret) > self::TWO_FACTOR_SECRET_STRING_LENGTH;
    }

    private function checkMod()
    {
        if (!$this->isModValid()) {
            Header::redirect(
                $this->registry->site_url,
                t('No module found!'),
                Design::ERROR_TYPE
            );
        }
    }

    /**
     * @return bool
     */
    private function isModValid()
    {
        $aValidMods = [
            'user',
            'affiliate',
            PH7_ADMIN_MOD
        ];

        return in_array($this->sMod, $aValidMods, true);
    }
}
