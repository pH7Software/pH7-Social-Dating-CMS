<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Asset / Ajax
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token;

class AdsAjax
{
    /** @var HttpRequest */
    private $oHttpRequest;

    /** @var AdsCoreModel */
    private $oAdsModel;

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        if (!(new Token)->check('ads')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        $this->oHttpRequest = new HttpRequest;
        $this->oAdsModel = new AdsCoreModel;

        switch ($this->oHttpRequest->post('type')) {
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
                Http::setHeadersByCode(400);
                exit('Bad Request Error');
        }
    }

    protected function activate()
    {
        $sTable = AdsCore::getTable();

        $this->bStatus = $this->oAdsModel->setStatus($this->oHttpRequest->post('adsId'), 1, $sTable);

        if ($this->bStatus) {
            (new Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear();
            $this->sMsg = jsonMsg(1, t('The banner has been activated.'));
        } else {
            $this->sMsg = jsonMsg(0, t('Cannot activate the banner. Please try later.'));
        }
        echo $this->sMsg;
    }

    protected function deactivate()
    {
        $sTable = AdsCore::getTable();

        $this->bStatus = $this->oAdsModel->setStatus($this->oHttpRequest->post('adsId'), 0, $sTable);

        if ($this->bStatus) {
            (new Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear();
            $this->sMsg = jsonMsg(1, t('The banner has been deactivated.'));
        } else {
            $this->sMsg = jsonMsg(0, t('Cannot deactivate the banner. Please try later.'));
        }
        echo $this->sMsg;
    }

    protected function delete()
    {
        $sTable = AdsCore::getTable();

        $this->bStatus = $this->oAdsModel->delete($this->oHttpRequest->post('adsId'), $sTable);

        if ($this->bStatus) {
            /* Clean AdminCoreModel Ads and Model\Design for STATIC data */
            (new Cache)->start(Design::CACHE_STATIC_GROUP, null, null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalAds', null)->clear()
                ->start(AdsCoreModel::CACHE_GROUP, 'totalAdsAffiliates', null)->clear();

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
