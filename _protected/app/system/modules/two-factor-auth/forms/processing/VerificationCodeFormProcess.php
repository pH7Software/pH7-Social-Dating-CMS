<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Lost Password / Form / Processing
 */
namespace PH7;
defined('PH7') or die('Restricted access');

class ValidationCodeFormProcess extends Form
{
    public function __construct($sMod)
    {
        parent::__construct();

        $oAuthenticator = new \PHPGangsta_GoogleAuthenticator();

        $iProfileId = $this->session->get('2fa_profile_id');
        $o2FactorModel = new TwoFactorAuthModel($sMod);
        $sSecret = $o2FactorModel->getSecret($iProfileId);
        $sCode = $this->httprequest->post('verification_code');

        $bCheck = $oAuthenticator->verifyCode($sSecret, $sCode, 0); 

        if ($bCheck)
        {
        	$sCoreClass = $this->getClassName($sMod);
        	$sCoreModelClass = $sCoreClass . 'Model';
        	(new $sCoreClass)->setAuth($iProfileId, $sCoreModelClass, $this->session, new PH7\Framework\Mvc\Model\Security);
        	Framework\Url\Header::redirect(Uri::get($sMod, 'main', 'index'), t('You are successfully logged!'));
        }

    }

    /**
     * Get main user core class according to the module.
     *
     * @param string $sMod Module name.
     * @return string Correct class nlass name
     * @throws \PH7\Framework\Error\CException\PH7InvalidArgumentException Explanatory message if the specified module is wrong.
     */
    protected function getClassName($sMod)
    {
    	switch ($sMod)
    	{
    		case 'user':
    			$oClass = 'UserCore';
    		break;

    		case PH7_ADMIN_MOD:
    		 	$oClass = 'AdminCore';
    		break;

    		case 'affiliate':
    		 	$oClass = 'AffiliateCore';
    		break;

    		default:
    			throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Wrong "' . $sMod . '" module specified to get the class name');
    	}

    	return $sMod;
    }

}
