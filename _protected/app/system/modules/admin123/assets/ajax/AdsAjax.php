<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\Design as DesignModel;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token;
use Teapot\StatusCode;

class AdsAjax
{
    /** @var AdsCoreModel */
    private $oAdsModel;

    /** @var string */
    private $sMsg;

    /** @var string */
    private $sTable;

    /** @var int */
    private $iAdId;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        if (!(new Token)->check('ads')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        $oHttpRequest = new HttpRequest;
        $this->oAdsModel = new AdsCoreModel;
        $this->sTable = $oHttpRequest->post('table');
        $this->iAdId = $oHttpRequest->post('adId');

        switch ($oHttpRequest->post('type')) {
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
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error');
        }
    }

    protected function activate()
    {
        $this->bStatus = $this->oAdsModel->setStatus($this->iAdId, AdsCoreModel::ACTIVE, $this->sTable);

        if ($this->bStatus) {
            (new Cache)->start(DesignModel::CACHE_STATIC_GROUP, null, null)->clear();
            $this->sMsg = jsonMsg(1, t('The banner has been activated.'));
        } else {
            $this->sMsg = jsonMsg(0, t('Cannot activate the banner. Please try later.'));
        }
        echo $this->sMsg;
    }

    protected function deactivate()
    {
        $this->bStatus = $this->oAdsModel->setStatus($this->iAdId, AdsCoreModel::DEACTIVATE, $this->sTable);

        if ($this->bStatus) {
            (new Cache)->start(DesignModel::CACHE_STATIC_GROUP, null, null)->clear();
            $this->sMsg = jsonMsg(1, t('The banner has been deactivated.'));
        } else {
            $this->sMsg = jsonMsg(0, t('Cannot deactivate the banner. Please try later.'));
        }
        echo $this->sMsg;
    }

    protected function delete()
    {
        $this->bStatus = $this->oAdsModel->delete($this->iAdId, $this->sTable);

        if ($this->bStatus) {
            /* Clean AdminCoreModel Ads and Model\Design for STATIC data */
            (new Cache)
                ->start(DesignModel::CACHE_STATIC_GROUP, null, null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalads', null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalads_affiliates', null)->clear();

            $this->sMsg = jsonMsg(1, t('The banner has been deleted.'));
        } else {
            $this->sMsg = jsonMsg(0, t('Cannot remove the banner. Please try later.'));
        }
        echo $this->sMsg;
    }
}

// Only for the Admins
if (Admin::auth()) {
    new AdsAjax;
}
