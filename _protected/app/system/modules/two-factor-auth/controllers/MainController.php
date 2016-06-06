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

	public function __construct()
	{
		parent::__construct();

		$this->iProfileId = $this->session->get('member_id');
	}

    public function verificationCode($sMod = '')
    {
        $this->view->page_title = $this->view->h2_title = t('Verification Code');
        $this->output();
    }

    public function setup($sMod = '')
    {
    	$this->sMod = $sMod;

        $this->o2FactorModel = new TwoFactorAuthModel($this->sMod);

        $this->checkMod();

        $this->view->page_title = $this->view->h2_title = t('Two-Factor Authentication');
        $this->view->mod = $this->sMod;

        $this->view->is_enabled = $bIsEnabled = $this->o2FactorModel->isEnabled($this->iProfileId);

        if ($this->httpRequest->postExists('status')) {
            $this->o2FactorModel->setStatus( (int)$bIsEnabled );
        }

        $oAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $sSecret = $oAuthenticator->createSecret();
        $this->o2FactorModel->setSecret($sSecret);

        $this->view->qr_core = $oAuthenticator->getQRCodeGoogleUrl($this->registry->site_name, $sSecret, $this->registry->site_url);

        $this->output();
    }

    private function checkMod()
    {
        if ($this->sMod !== 'user' && $this->sMod !== 'affiliate' && $this->sMod !== PH7_ADMIN_MOD)
            Header::redirect($this->registry->site_url, t('No module found!'), 'error');
    }

}
