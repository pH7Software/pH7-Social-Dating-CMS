<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
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

        if (AdminCore::auth() && !Affiliate::auth() && $this->httpRequest->getExists('profile_id')) {
            $iProfileId = $this->httpRequest->get('profile_id', 'int');
        } else {
            $iProfileId = $this->session->get('affiliate_id');
        }

        $oAffModel = new AffiliateModel;
        $oAff = $oAffModel->readProfile($iProfileId, DbTableName::AFFILIATE);

        if (!$this->str->equals($this->httpRequest->post('bank_account'), $oAff->bankAccount)) {
            $oAffModel->updateProfile('bankAccount', $this->httpRequest->post('bank_account'), $iProfileId, DbTableName::AFFILIATE);
        }

        unset($oAffModel, $oAff);

        /* Clean Affiliate UserCoreModel / readProfile Cache */
        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'readProfile' . $iProfileId . DbTableName::AFFILIATE, null)->clear();

        \PFBC\Form::setSuccess('form_bank_account', t('Your bank information has been successfully updated!'));
    }
}
