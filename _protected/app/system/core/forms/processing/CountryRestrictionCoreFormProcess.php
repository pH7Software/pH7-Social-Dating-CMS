<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */

declare(strict_types=1);

namespace PH7;

use PH7\Datatype\Type;
use PH7\Framework\Cache\Cache;

defined('PH7') or exit('Restricted access');

class CountryRestrictionCoreFormProcess extends Form
{
    private const COUNTRY_CODE_LENGTH = 2;

    private string $sTable;

    /**
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    public function __construct(string $sTable)
    {
        parent::__construct();

        $this->sTable = $sTable;
        $oUserModel = new UserCoreModel;

        // Firstly, clear everything
        $oUserModel->clearCountries($this->sTable);

        // Secondly, reindex the table
        $aCountries = $this->httpRequest->post('countries', Type::ARRAY);
        foreach ($aCountries as $sCountry) {
            if ($this->isEligibleToAdd($sCountry)) {
                $oUserModel->addCountry($sCountry, $this->sTable);
            }
        }
        unset($oUserModel);

        $this->clearCache();

        \PFBC\Form::setSuccess('form_country_restriction', t('Successfully saved!'));
    }

    private function isEligibleToAdd(string $sCountryCode): bool
    {
        return !empty(trim($sCountryCode)) &&
            strlen($sCountryCode) === self::COUNTRY_CODE_LENGTH &&
            $this->isCountryCodeUppercase($sCountryCode);
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
