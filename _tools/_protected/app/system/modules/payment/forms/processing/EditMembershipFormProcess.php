<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class EditMembershipFormProcess extends Form
{
    /** @var array */
    private static $aFields = [
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

        /* Clean UserCoreModel Cache */
        (new Cache)->start(UserCoreModel::CACHE_GROUP, null, null)->clear();

        Header::redirect(
            Uri::get('payment', 'admin', 'membershiplist'),
            t('The Membership has been saved successfully!')
        );
    }

    /**
     * Update fields into the DB (the modified ones only).
     *
     * @param PaymentModel $oPayModel
     * @param int $iGroupId
     *
     * @return void
     */
    private function updateTextFields(PaymentModel $oPayModel, $iGroupId)
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
     *
     * @return void
     */
    private function updatePermsFields(PaymentModel $oPayModel, $iGroupId)
    {
        $aPerms = serialize($this->httpRequest->post('perms'));
        $oPayModel->updateMembershipGroup('permissions', $aPerms, $iGroupId);
    }
}
