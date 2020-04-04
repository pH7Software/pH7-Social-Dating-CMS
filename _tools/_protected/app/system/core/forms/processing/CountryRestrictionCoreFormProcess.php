<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Form / Processing
 */

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
        foreach ($this->httpRequest->post('countries') as $sCountry) {
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
            UserCoreModel::CACHE_GROUP,
            'countriesList' . $this->sTable,
            null
        )->clear();
    }
}
