<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Mvc\Request\Http,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\HeaderUrl;

class AdsFormProcess extends Form
{

    public function __construct()
    {
        parent::__construct();

        $sTable = AdsCore::getTable();
        (new AdsCoreModel)->add($this->httpRequest->post('title'), $this->httpRequest->post('code', Http::NO_CLEAN), $sTable);

        /* Clean AdminCoreModel Ads and Model\Design for STATIC data */
        (new Framework\Cache\Cache)->start(Framework\Mvc\Model\Design::CACHE_STATIC_GROUP, null, null)->clear()
        ->start(AdsCoreModel::CACHE_GROUP, 'totalAds', null)->clear()
        ->start(AdsCoreModel::CACHE_GROUP, 'totalAdsAffiliates', null)->clear();

        $sSlug = (AdsCore::getTable() == 'AdsAffiliates') ? 'affiliate' : '';
        HeaderUrl::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'ads', $sSlug), t('The Advertisement was added successfully!'));
    }

}
