<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

class BankFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $oAffModel = new AffiliateModel;
        $iProfileId = (AdminCore::auth() && !Affiliate::auth() && $this->httpRequest->getExists('profile_id')) ? $this->httpRequest->get('profile_id', 'int') : $this->session->get('affiliate_id');
        $oAff = $oAffModel->readProfile($iProfileId, 'Affiliates');

        if (!$this->str->equals($this->httpRequest->post('bank_account'), $oAff->bankAccount))
            $oAffModel->updateProfile('bankAccount', $this->httpRequest->post('bank_account'), $iProfileId, 'Affiliates');

        unset($oAffModel, $oAff);

        /* Clean Affiliate UserCoreModel / readProfile Cache */
        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'readProfile' . $iProfileId . 'Affiliates', null)->clear();

        \PFBC\Form::setSuccess('form_bank_account', t('Your bank information has been successfully updated!'));
    }

}
