<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Payment / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditMembershipFormProcess extends Form
{
    private static array $aFields = [
        'name' => 'name',
        'description' => 'description',
        'price' => 'price',
        'expiration_days' => 'expirationDays',
        'enable' => 'enable'
    ];

    public function __construct()
    {
        parent::__construct();

        $oPayModel = new PaymentModel;
        $iGroupId = $this->httpRequest->get('group_id', 'int');

        $this->updateTextFields($oPayModel, $iGroupId);
        $this->updatePermsFields($oPayModel, $iGroupId);

        unset($oPayModel);

        $this->clearCache();
        $this->redirectToMembershipList();
    }

    /**
     * Update fields into the DB (the modified ones only).
     *
     * @param PaymentModel $oPayModel
     * @param int $iGroupId
     */
    private function updateTextFields(PaymentModel $oPayModel, $iGroupId): void
    {
        $oMembership = $oPayModel->getMemberships($iGroupId);

        foreach (self::$aFields as $sKey => $sVal) {
            if (!$this->str->equals($this->httpRequest->post($sKey), $oMembership->$sVal)) {
                $oPayModel->updateMembershipGroup($sVal, $this->httpRequest->post($sKey), $oMembership->groupId);
            }
        }
    }

    /**
     * Update serialized permission data.
     *
     * @param PaymentModel $oPayModel
     * @param int $iGroupId
     */
    private function updatePermsFields(PaymentModel $oPayModel, $iGroupId): void
    {
        $aPerms = serialize($this->httpRequest->post('perms'));
        $oPayModel->updateMembershipGroup('permissions', $aPerms, $iGroupId);
    }

    private function redirectToMembershipList(): void
    {
        Header::redirect(
            Uri::get('payment', 'admin', 'membershiplist'),
            t('The Membership has been saved successfully!')
        );
    }

    private function clearCache(): void
    {
        // Clear UserCoreModel Cache
        (new Cache)->start(UserCoreModel::CACHE_GROUP, null, null)->clear();
    }
}
