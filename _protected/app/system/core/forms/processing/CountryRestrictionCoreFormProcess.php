<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Cache\Cache;

defined('PH7') or exit('Restricted access');

class CountryRestrictionCoreFormProcess extends Form
{
    const COUNTRY_CODE_LENGTH = 2;

    /** @var string */
    private $sTable;

    /**
     * @param string $sTable
     *
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct($sTable)
    {
        parent::__construct();

        $this->sTable = $sTable;
        $oUserModel = new UserCoreModel;

        // First, clear everything
        $oUserModel->clearCountries($this->sTable);

        // Then, reindex the table
        $aCountries = (array)$this->httpRequest->post('countries');
        if ($this->areCountriesNotSet($aCountries)) {
            \PFBC\Form::setError('form_country_restriction', t('You need to select at least one country.'));
            return;
        }

        foreach ($aCountries as $sCountry) {
            if ($this->isEligibleToAdd($sCountry)) {
                $oUserModel->addCountry($sCountry, $this->sTable);
            }
        }
        unset($oUserModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_country_restriction', t('Successfully saved!'));
    }

    /**
     * @param string $sCountryCode
     *
     * @return bool
     */
    private function isEligibleToAdd($sCountryCode)
    {
        return !empty(trim($sCountryCode)) &&
            strlen($sCountryCode) === self::COUNTRY_CODE_LENGTH &&
            $this->isCountryCodeUppercase($sCountryCode);
    }

    private function areCountriesNotSet(array $aCountries)
    {
        return empty($aCountries) || count($aCountries) === 1 && empty($aCountries[0]);
    }

    private function isCountryCodeUppercase(string $sCountryCode): bool
    {
        return strtoupper($sCountryCode) === $sCountryCode;
    }

    private function clearCache(): void
    {
        (new Cache)->start(
            UserCoreModel::CACHE_GROUP,
            'countriesList' . $this->sTable,
            null
        )->clear();
    }
}
