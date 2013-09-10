<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Form / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Url\HeaderUrl, PH7\Framework\Mvc\Router\Uri;

class EditMembershipFormProcessing extends Form
{

    public function __construct()
    {
        parent::__construct();

        $iGroupId = $this->httpRequest->get('group_id', 'int');
        $oPayModel = new PaymentModel;
        $oMembership = $oPayModel->getMemberships($iGroupId);

        if (!$this->str->equals($this->httpRequest->post('name'), $oMembership->name))
            $oPayModel->updateMembershipGroup('name', $this->httpRequest->post('name'), $iGroupId);

        if (!$this->str->equals($this->httpRequest->post('description'), $oMembership->description))
            $oPayModel->updateMembershipGroup('description', $this->httpRequest->post('description'), $iGroupId);

        $aPerms = serialize($this->httpRequest->post('perms'));
        $oPayModel->updateMembershipGroup('permissions', $aPerms, $iGroupId);

        if (!$this->str->equals($this->httpRequest->post('price'), $oMembership->price))
            $oPayModel->updateMembershipGroup('price', $this->httpRequest->post('price'), $iGroupId);

        if (!$this->str->equals($this->httpRequest->post('expiration_days'), $oMembership->expirationDays))
            $oPayModel->updateMembershipGroup('expirationDays', $this->httpRequest->post('expiration_days'), $iGroupId);

        if (!$this->str->equals($this->httpRequest->post('enable'), $oMembership->enable))
            $oPayModel->updateMembershipGroup('enable', $this->httpRequest->post('enable'), $iGroupId);

        (new Framework\Cache\Cache)->start(UserCoreModel::CACHE_GROUP, 'memberships' . $iGroupId, null)->clear();

        unset($oPayModel);

        HeaderUrl::redirect(Uri::get('payment','admin','membershiplist'), t('The Membership has been saved successfully!'));
    }

}
