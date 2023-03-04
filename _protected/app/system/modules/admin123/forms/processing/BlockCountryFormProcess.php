<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Datatype\Type;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Mvc\Model\BlockCountry as BlockCountryModel;

class BlockCountryFormProcess extends Form
{
    private const COUNTRY_CODE_LENGTH = 2;

    public function __construct()
    {
        parent::__construct();

        $oBlockCountryModel = new BlockCountryModel;

        // First, clear everything
        $oBlockCountryModel->clear();

        // Then, reindex the table
        $aCountries = $this->httpRequest->post('countries', Type::ARRAY);
        foreach ($aCountries as $sCountry) {
            if ($this->isEligibleToAdd($sCountry)) {
                $oBlockCountryModel->add($sCountry);
            }
        }
        unset($oBlockCountryModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_country_blocklist', t('Successfully saved!'));
    }

    private function isEligibleToAdd(string $sCountryCode): bool
    {
        return !empty(trim($sCountryCode)) && strlen($sCountryCode) === self::COUNTRY_CODE_LENGTH &&
            $this->isCountryCodeUppercase($sCountryCode);
    }

    private function isCountryCodeUppercase(string $sCountryCode): bool
    {
        return strtoupper($sCountryCode) === $sCountryCode;
    }

    private function clearCache(): void
    {
        (new Cache)->start(
            BlockCountryModel::CACHE_GROUP,
            null,
            null
        )->clear();
    }
}
