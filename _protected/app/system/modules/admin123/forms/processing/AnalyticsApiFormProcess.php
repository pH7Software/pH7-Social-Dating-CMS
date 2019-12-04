<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\Analytics;
use PH7\Framework\Mvc\Model\Design;
use PH7\Framework\Mvc\Request\Http;

class AnalyticsApiFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->str->equals(
            $this->httpRequest->post('code', Http::NO_CLEAN),
            (new Design)->analyticsApi(false))
        ) {
            (new Analytics)->updateApi($this->httpRequest->post('code', Http::NO_CLEAN));
            $this->clearCache();
        }
        \PFBC\Form::setSuccess('form_analytics', t('Analytics Code updated!'));
    }

    /**
     * Clear the 'active' "analyticsApi" data
     *
     * @return void
     */
    private function clearCache()
    {
        (new Cache)->start(Design::CACHE_STATIC_GROUP, 'analyticsApi1', null)->clear();
    }
}
