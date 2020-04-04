<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

class BankFormProcess extends Form
{
    /**
     * @param int $iProfileId
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($iProfileId)
    {
        parent::__construct();

        $oAffModel = new AffiliateModel;
        $oAff = $oAffModel->readProfile($iProfileId, DbTableName::AFFILIATE);

        if (!$this->str->equals($this->httpRequest->post('bank_account'), $oAff->bankAccount)) {
            $oAffModel->updateProfile('bankAccount', $this->httpRequest->post('bank_account'), $iProfileId, DbTableName::AFFILIATE);
        }

        unset($oAffModel, $oAff);

        $this->clearCache($iProfileId);

        \PFBC\Form::setSuccess('form_bank_account', t('Your bank information has been successfully updated!'));
    }

    /**
     * @param int $iProfileId
     */
    private function clearCache($iProfileId)
    {
        (new Framework\Cache\Cache)->start(
            UserCoreModel::CACHE_GROUP,
            'readProfile' . $iProfileId . DbTableName::AFFILIATE,
            null
        )->clear();
    }
}
