<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Security\Security,
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Util\Various,
PH7\Framework\Cookie\Cookie,
PH7\Framework\Ip\Ip,
PH7\Framework\Date\CDateTime,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class JoinFormProcess extends Form
{

    private $oUserModel, $oRegistration, $iActiveType;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new UserModel;
        $this->oRegistration = new Registration;
        $this->iActiveType = DbConfig::getSetting('userActivationType');
    }

    public function step1()
    {
        $iAffId = (int) (new Cookie)->get(AffiliateCore::COOKIE_NAME);
        $sRef = ($this->session->exists('joinRef')) ? $this->session->get('joinRef') : t('No reference'); // Statistics
        $this->session->remove('joinRef');

        $aData = [
            'email' => $this->httpRequest->post('mail'),
            'username' => $this->httpRequest->post('username'),
            'first_name' => $this->httpRequest->post('first_name'),
            'reference' => $sRef,
            'ip' => Ip::get(),
            'hash_validation' => Various::genRnd(),
            'current_date' => (new CDateTime)->get()->dateTime('Y-m-d H:i:s'),
            'is_active' => $this->iActiveType,
            'group_id' => (int) DbConfig::getSetting('defaultMembershipGroupId'),
            'affiliated_id' => $iAffId
        ];
        $aData += ['password' => Security::hashPwd($this->httpRequest->post('password'))];

        $iTimeDelay = (int) DbConfig::getSetting('timeDelayUserRegistration');
        if (!$this->oUserModel->checkWaitJoin($aData['ip'], $iTimeDelay, $aData['current_date']))
        {
            \PFBC\Form::setError('form_join_user', Form::waitRegistrationMsg($iTimeDelay));
        }
        elseif (!$this->oUserModel->join($aData))
        {
            \PFBC\Form::setError('form_join_user', t('An error occurred during registration!<br />
            Please try again with other information in the form fields or come back later.'));
        }
        else
        {
            // Successful registration in the database for step 1!

            /** Update the Affiliate Commission **/
            if ($this->iActiveType == 0) // Only if the user's account is already activated.
                AffiliateCore::updateJoinCom($iAffId, $this->config, $this->registry);

            // Send email
            $this->oRegistration->sendMail($aData);

            $this->session->set('mail_step1', $this->httpRequest->post('mail'));
            Header::redirect(Uri::get('user','signup','step2'));
        }
    }

    public function step2()
    {
        $iProfileId = $this->oUserModel->getId($this->session->get('mail_step1'));
        $sBirthDate = $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d');

        // WARNING FOT "matchSex" FIELD: Be careful, you should use the \PH7\Framework\Mvc\Request\Http::ONLY_XSS_CLEAN constant otherwise the post method of the HttpRequest class removes the tags special
        // and damages the SET function SQL for entry into the database
        $aData1 = [
            'sex' => $this->httpRequest->post('sex'),
            'match_sex' => Form::setVal($this->httpRequest->post('match_sex', Http::ONLY_XSS_CLEAN)),
            'birth_date' => $sBirthDate,
            'profile_id' => $iProfileId
        ];

        $aData2 = [
            'country' => $this->httpRequest->post('country'),
            'city' => $this->httpRequest->post('city'),
            'state' => $this->httpRequest->post('state'),
            'zip_code' => $this->httpRequest->post('zip_code'),
            'profile_id' => $iProfileId
        ];

        if (!$this->oUserModel->exe($aData1, '2_1') || !$this->oUserModel->exe($aData2, '2_2'))
        {
            \PFBC\Form::setError('form_join_user2', t('An error occurred during registration!<br /> Please try again with other information in the form fields or come back later.'));
        }
        else
        {
            // Register successfully in database for step 2!
            $this->session->set('mail_step2', $this->session->get('mail_step1'));
            Header::redirect(Uri::get('user','signup','step3'));
        }
    }

    public function step3()
    {

        $aData = [
            'description' => $this->httpRequest->post('description', Http::ONLY_XSS_CLEAN),
            'profile_id' => $this->oUserModel->getId($this->session->get('mail_step2'))
        ];

        if (!$this->oUserModel->exe($aData, '3'))
        {
            \PFBC\Form\setError('form_join_user3', t('An error occurred during registration!<br /> Please try again with other information in the form fields or come back later.'));
        }
        else
        {
            $this->session->destroy(); // Remove all sessions created pending registration

            Header::redirect(Uri::get('user','main','login'), t('You now been registered! %0%', $this->oRegistration->getMsg()));
        }
    }

    public function __destruct()
    {
        unset($this->oUserModel, $this->iActiveType);
    }

}
