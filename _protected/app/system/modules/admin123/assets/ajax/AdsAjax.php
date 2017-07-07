<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Asset / Ajax
 */

namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Request\Http;

class AdsAjax
{

    private $_oHttpRequest, $_oAdsModel, $_sMsg, $_bStatus;

    public function __construct()
    {
        if (!(new Framework\Security\CSRF\Token)->check('ads'))
            exit(jsonMsg(0, Form::errorTokenMsg()));

        $this->_oHttpRequest = new Http;
        $this->_oAdsModel = new AdsCoreModel;

        switch ($this->_oHttpRequest->post('type')) {
            case 'activate':
                $this->activate();
                break;

            case 'deactivate':
                $this->deactivate();
                break;

            case 'delete':
                $this->delete();
                break;

            default:
                Framework\Http\Http::setHeadersByCode(400);
                exit('Bad Request Error');
        }
    }

    protected function activate()
    {
        $sTable = AdsCore::getTable();

        $this->_bStatus = $this->_oAdsModel->setStatus($this->_oHttpRequest->post('adsId'), 1, $sTable);

        if ($this->_bStatus) {
            (new Framework\Cache\Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear();
            $this->_sMsg = jsonMsg(1, t('The banner has been activated.'));
        } else {
            $this->_sMsg = jsonMsg(0, t('Cannot activate the banner. Please try later.'));
        }
        echo $this->_sMsg;
    }

    protected function deactivate()
    {
        $sTable = AdsCore::getTable();

        $this->_bStatus = $this->_oAdsModel->setStatus($this->_oHttpRequest->post('adsId'), 0, $sTable);

        if ($this->_bStatus) {
            (new Framework\Cache\Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear();
            $this->_sMsg = jsonMsg(1, t('The banner has been deactivated.'));
        } else {
            $this->_sMsg = jsonMsg(0, t('Cannot deactivate the banner. Please try later.'));
        }
        echo $this->_sMsg;
    }

    protected function delete()
    {
        $sTable = AdsCore::getTable();

        $this->_bStatus = $this->_oAdsModel->delete($this->_oHttpRequest->post('adsId'), $sTable);

        if ($this->_bStatus) {
            /* Clean AdminCoreModel Ads and Model\Design for STATIC data */
            (new Framework\Cache\Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalAds', null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalAdsAffiliates', null)->clear();

            $this->_sMsg = jsonMsg(1, t('The banner has been deleted.'));
        } else {
            $this->_sMsg = jsonMsg(0, t('Cannot remove the banner. Please try later.'));
        }
        echo $this->_sMsg;
    }

    public function __destruct()
    {
        unset($this->_oHttpRequest, $this->_oAdsModel, $this->_sMsg, $this->_bStatus);
    }

}

// Only for the Admins
if (Admin::auth())
    new AdsAjax;
