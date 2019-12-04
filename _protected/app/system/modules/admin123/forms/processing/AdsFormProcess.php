<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Design as DesignModel;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdsFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $bIsAff = (AdsCore::getTable() === AdsCore::AFFILIATE_AD_TABLE_NAME);

        $sTable = AdsCore::getTable();
        $sTitle = $this->httpRequest->post('title');
        $sCode = $this->httpRequest->post('code', Http::NO_CLEAN);
        $aSize = explode('x', $this->httpRequest->post('size'));
        $iWidth = $aSize[0];
        $iHeight = $aSize[1];

        (new AdsCoreModel)->add($sTitle, $sCode, $iWidth, $iHeight, $sTable);

        $this->clearCache($bIsAff);

        $sSlug = $bIsAff ? 'affiliate' : '';
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'setting', 'ads', $sSlug),
            t('The banner has been added!')
        );
    }

    /**
     * Clean AdminCoreModel Ads and Model\Design for STATIC data.
     *
     * @param bool $bIsAffiliate
     *
     * @return void
     */
    private function clearCache($bIsAffiliate)
    {
        (new Cache)
            ->start(DesignModel::CACHE_STATIC_GROUP, null, null)->clear()
            ->start(AdsCoreModel::CACHE_GROUP, 'totalads' . ($bIsAffiliate ? '_affiliates' : ''), null)->clear();
    }
}
