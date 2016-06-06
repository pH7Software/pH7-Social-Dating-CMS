<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Controller
 */

namespace PH7;

use PH7\Framework\Url\Header;

class MainController extends Controller
{
    private $o2FactorModel, $sMod, $iProfileId;

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

        $this->view->is_enabled = $bIsEnabled = (int) $this->o2FactorModel->isEnabled($this->iProfileId);

        if ($this->httpRequest->postExists('status')) {
            $bIsEnabled = ($bIsEnabled == 1 ? 0 : 1); // Get the opposite value (if 1 so 0 | if 0 so 1)
            $this->o2FactorModel->setStatus($bIsEnabled, $this->iProfileId);
        }

        $oAuthenticator = new \PHPGangsta_GoogleAuthenticator;
        $sSecret = $oAuthenticator->createSecret();
        $this->o2FactorModel->setSecret($sSecret, $this->iProfileId);

        $this->view->qr_core = $oAuthenticator->getQRCodeGoogleUrl('pH7CMS', $sSecret, $this->registry->site_url);

        $this->output();
    }

    /**
     * Get Session Profile ID.
     *
     * @return integer
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException Explanatory message if the specified module is wrong.
     */
    protected function getProfileId()
    {
        switch ($this->sMod)
        {
            case 'user':
                return $this->session->get('member_id');
            break;
            case 'affiliate':
                return $this->session->get('affiliate_id');
            break;
            case PH7_ADMIN_MOD:
                return $this->session->get('admin_id');
            break;

            default:
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Wrong "' . $this->sMod . '" module!');
        }
    }

    private function checkMod()
    {
        if ($this->sMod !== 'user' && $this->sMod !== 'affiliate' && $this->sMod !== PH7_ADMIN_MOD)
            Header::redirect($this->registry->site_url, t('No module found!'), 'error');
    }

}
