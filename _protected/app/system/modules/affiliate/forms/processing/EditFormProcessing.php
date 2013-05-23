<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class EditFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oAffModel = new AffiliateModel;
        $iProfileId = (AdminCore::auth() && !Affiliate::auth() && $this->httpRequest->getExists('profile_id')) ? $this->httpRequest->get('profile_id', 'int') : $this->session->get('affiliate_id');
        $oAff = $oAffModel->readProfile($iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('first_name'), $oAff->firstName)) {
            $oAffModel->updateProfile('firstName', $this->httpRequest->post('first_name'), $iProfileId, 'Affiliate');
            $this->session->set('affiliate_first_name', $this->httpRequest->post('first_name'));

            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'firstName' . $iProfileId . 'Affiliate', null)->clear();
        }

        if(!$this->str->equals($this->httpRequest->post('last_name'), $oAff->lastName))
            $oAffModel->updateProfile('lastName', $this->httpRequest->post('last_name'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('phone'), $oAff->phone))
            $oAffModel->updateProfile('phone', $this->httpRequest->post('phone'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('sex'), $oAff->sex)) {
            $oAffModel->updateProfile('sex', $this->httpRequest->post('sex'), $iProfileId, 'Affiliate');
            $this->session->set('affiliate_sex', $this->httpRequest->post('sex'));

            (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'sex' . $iProfileId . 'Affiliate', null)->clear();
        }

        if(!$this->str->equals($this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d'), $oAff->birthDate))
            $oAffModel->updateProfile('birthDate', $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('address'), $oAff->address))
            $oAffModel->updateProfile('address', $this->httpRequest->post('address'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('country'), $oAff->country))
            $oAffModel->updateProfile('country', $this->httpRequest->post('country'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('city'), $oAff->city))
            $oAffModel->updateProfile('city', $this->httpRequest->post('city'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('state'), $oAff->state))
            $oAffModel->updateProfile('state', $this->httpRequest->post('state'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('zip_code'), $oAff->zipCode))
            $oAffModel->updateProfile('zipCode', $this->httpRequest->post('zip_code'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('description'), $oAff->description))
            $oAffModel->updateProfile('description', $this->httpRequest->post('description'), $iProfileId, 'Affiliate');

        if(!$this->str->equals($this->httpRequest->post('website'), $oAff->website))
            $oAffModel->updateProfile('website', $this->httpRequest->post('website'), $iProfileId, 'Affiliate');

        $oAffModel->setLastEdit($iProfileId, 'Affiliate');

        unset($oAffModel, $oAff);

        (new Affiliate)->clearReadProfileCache($iProfileId, 'Affiliate');

        \PFBC\Form::setSuccess('form_aff_edit_account', t('Your profile has been saved successfully!'));
    }

}
