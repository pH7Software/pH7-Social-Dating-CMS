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
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Request\Http;

class UpdateAdsFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $sTable = AdsCore::getTable();

        (new AdsCoreModel)->update(
            $this->httpRequest->post('id_ads'),
            $this->httpRequest->post('title'),
            $this->httpRequest->post('code', Http::NO_CLEAN),
            $sTable
        );

        $this->clearCache();

        \PFBC\Form::setSuccess('form_update_ads', t('The banner has been saved!'));
    }

    private function clearCache()
    {
        (new Cache)->start(
            Design::CACHE_STATIC_GROUP,
            null,
            null
        )->clear();
    }
}
