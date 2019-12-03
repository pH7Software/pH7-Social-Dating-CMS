<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\BlockCountry as BlockCountryModel;

class BlockCountryFormProcess extends Form
{
    const COUNTRY_CODE_LENGTH = 2;

    public function __construct()
    {
        parent::__construct();

        $oBlockCountryModel = new BlockCountryModel;

        // First, clear everything
        $oBlockCountryModel->clear();

        // Then, reindex the table
        foreach ($this->httpRequest->post('countries') as $sCountry) {
            if ($this->isEligibleToAdd($sCountry)) {
                $oBlockCountryModel->add($sCountry);
            }
        }
        unset($oBlockCountryModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_country_blocklist', t('Successfully saved!'));
    }

    /**
     * @param string $sCountryCode
     *
     * @return bool
     */
    private function isEligibleToAdd($sCountryCode)
    {
        return !empty(trim($sCountryCode)) && strlen($sCountryCode) === self::COUNTRY_CODE_LENGTH &&
            $this->isCountryCodeUppercase($sCountryCode);
    }

    /**
     * @param string $sCountryCode
     *
     * @return bool
     */
    private function isCountryCodeUppercase($sCountryCode)
    {
        return strtoupper($sCountryCode) === $sCountryCode;
    }

    private function clearCache()
    {
        (new Cache)->start(
            BlockCountryModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
