<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\BlockCountry as BlockCountryModel;

class BlockCountryFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oBlockCountryModel = new BlockCountryModel;

        // First of all, clear everything
        $oBlockCountryModel->clear();

        // Then, reindex the table
        foreach ($this->httpRequest->post('countries') as $sCountry) {
            $oBlockCountryModel->add($sCountry);
        }
        unset($oBlockCountryModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_country_blocklist', t('Successfully saved!'));
    }

    private function clearCache()
    {
        (new Cache)->start(BlockCountryModel::CACHE_GROUP, null, null)->clear();
    }
}
