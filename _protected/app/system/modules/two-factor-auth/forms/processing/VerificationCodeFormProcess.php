<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Two-Factor Auth / Form / Processing
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
            $oUserData = $sCoreModelClass->readProfile($iProfileId, Various::convertModToTable($sMod));
            (new $sCoreClass)->setAuth($oUserData, $sCoreModelClass, $this->session, new PH7\Framework\Mvc\Model\Security);

            $sUrl = ($sMod == PH7_ADMIN_MOD) ? Uri::get(PH7_ADMIN_MOD, 'main', 'index') : $Uri::get($sMod, 'account', 'index');
            Framework\Url\Header::redirect($sUrl, t('You are successfully logged!'));
        }
        else
        {
            \PFBC\Form::setError('form_verification_code', t('Oops! The Verification Code is incorrect. Please try again.')));
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

            case 'affiliate':
                 $oClass = 'AffiliateCore';
            break;

            case PH7_ADMIN_MOD:
                $oClass = 'AdminCore';
            break;

            default:
                throw new \PH7\Framework\Error\CException\PH7InvalidArgumentException('Wrong "' . $sMod . '" module specified to get the class name');
        }

        return $sMod;
    }

}
