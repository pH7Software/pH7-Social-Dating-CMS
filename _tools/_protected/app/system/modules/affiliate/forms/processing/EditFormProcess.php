<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Request\Http;

class EditFormProcess extends Form
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

        if (!$this->str->equals($this->httpRequest->post('first_name'), $oAff->firstName)) {
            $oAffModel->updateProfile(
                'firstName',
                $this->httpRequest->post('first_name'),
                $iProfileId,
                DbTableName::AFFILIATE
            );
            $this->session->set('affiliate_first_name', $this->httpRequest->post('first_name'));

            $this->clearFieldCache('firstName', $iProfileId);
        }

        if (!$this->str->equals($this->httpRequest->post('last_name'), $oAff->lastName)) {
            $oAffModel->updateProfile(
                'lastName',
                $this->httpRequest->post('last_name'),
                $iProfileId,
                DbTableName::AFFILIATE
            );
        }

        if (AdminCore::auth()) {
            // For security reasons, only admins can change profile gender
            if (!$this->str->equals($this->httpRequest->post('sex'), $oAff->sex)) {
                $oAffModel->updateProfile(
                    'sex',
                    $this->httpRequest->post('sex'),
                    $iProfileId,
                    DbTableName::AFFILIATE
                );
                $this->session->set('affiliate_sex', $this->httpRequest->post('sex'));

                $this->clearFieldCache('sex', $iProfileId);
            }
        }

        if (AdminCore::auth()) {
            // For security reasons, only admins can change date of birth
            if (!$this->str->equals($this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d'), $oAff->birthDate)) {
                $oAffModel->updateProfile(
                    'birthDate',
                    $this->dateTime->get($this->httpRequest->post('birth_date'))->date('Y-m-d'),
                    $iProfileId,
                    DbTableName::AFFILIATE
                );
            }
        }

        $this->updateDynamicFields($iProfileId, $oAffModel);


        $oAffModel->setLastEdit($iProfileId, DbTableName::AFFILIATE);

        $oAffCache = new Affiliate;
        $oAffCache->clearReadProfileCache($iProfileId, DbTableName::AFFILIATE);
        $oAffCache->clearInfoFieldCache($iProfileId, DbTableName::AFFILIATE_INFO);

        unset($oAffModel, $oAff, $oAffCache);

        \PFBC\Form::setSuccess(
            'form_aff_edit_account',
            t('The profile has been successfully updated')
        );
    }

    /**
     * Update affiliate's info fields.
     *
     * @param int $iProfileId
     * @param AffiliateCoreModel $oAffModel
     *
     * @return void
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function updateDynamicFields($iProfileId, AffiliateCoreModel $oAffModel)
    {
        $oFields = $oAffModel->getInfoFields($iProfileId, DbTableName::AFFILIATE_INFO);
        foreach ($oFields as $sColumn => $sValue) {
            $sHRParam = ($sColumn === 'description') ? Http::ONLY_XSS_CLEAN : null;
            if ($this->httpRequest->postExists($sColumn) && !$this->str->equals($this->httpRequest->post($sColumn, $sHRParam), $sValue)) {
                $oAffModel->updateProfile(
                    $sColumn,
                    $this->httpRequest->post($sColumn, $sHRParam),
                    $iProfileId,
                    DbTableName::AFFILIATE_INFO
                );
            }
        }
        unset($oFields);
    }

    /**
     * @param string $sCacheId
     * @param int $iProfileId
     *
     * @return void
     */
    private function clearFieldCache($sCacheId, $iProfileId)
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            $sCacheId . $iProfileId . DbTableName::AFFILIATE,
            null
        )->clear();
    }
}
