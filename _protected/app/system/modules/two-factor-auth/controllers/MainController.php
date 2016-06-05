<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Controller
 */

namespace PH7;

use PH7\Framework\Url\Header;

class MainController extends Controller
{
	private $oTwoFactorAuth, $sMod, $iProfileId;

	public function __construct()
	{
		parent::__construct();

		$this->iProfileId = $this->session->get('member_id');
	}

    public function setup($sMod = '')
    {
    	$this->sMod = $sMod;

        $this->oTwoFactorAuth = new TwoFactorAuth($this->sMod);

        $this->checkMod();

        $this->view->page_title = $this->view->h2_title = t('Two-Factor Authentication');
        $this->view->mod = $this->sMod;

        $this->view->is_enabled = $bIsEnabled = $this->oTwoFactorAuth->isEnabled($this->iProfileId, $this->sMod);

        if ($this->httpRequest->postExists('status')) {
            $this->oTwoFactorAuth->setStatus( (int)$bIsEnabled ));
        }

        $oAuthenticator = new \PHPGangsta_GoogleAuthenticator();
        $sSecret = $oAuthenticator->createSecret();

        $this->view->qr_core = $oAuthenticator->getQRCodeGoogleUrl($this->registry->site_name, $sSecret, $this->registry->site_url);

        $this->output();
    }

    private function checkMod()
    {
        if ($this->sMod !== 'user' && $this->sMod !== 'affiliate' && $this->sMod !== PH7_ADMIN_MOD)
            Header::redirect($this->registry->site_url, t('No module found!'), 'error');
    }

}
