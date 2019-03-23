<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Util\Various;

class FakerFactory
{
    /** @var int */
    private $iAmount;

    /** @var string */
    private $sLocale;

    /**
     * @param int $iAmount Number of profile to generate.
     * @param string $sLocale The locale if specified. e.g., en_US, en_IE, fr_FR, fr_BE, nl_NL, es_ES, ...
     */
    public function __construct($iAmount, $sLocale = Lang::DEFAULT_LOCALE)
    {
        $this->iAmount = $iAmount;
        $this->sLocale = $sLocale;
    }

    public function generateMembers()
    {
        $oUserModel = new UserCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement(
                [
                    GenderTypeUserCore::MALE,
                    GenderTypeUserCore::FEMALE,
                    GenderTypeUserCore::COUPLE
                ]
            );
            $sMatchSex = $oFaker->randomElement(
                [
                    GenderTypeUserCore::MALE,
                    GenderTypeUserCore::FEMALE,
                    GenderTypeUserCore::COUPLE
                ]
            );
            $iMaxAge = DbConfig::getSetting('maxAgeRegistration');
            $iMinAge = DbConfig::getSetting('minAgeRegistration');
            $sBirthDate = $oFaker->dateTimeBetween(
                sprintf('-%s years', $iMaxAge),
                sprintf('-%s years', $iMinAge)
            )->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = Cleanup::username($oFaker->userName, DbConfig::getSetting('maxUsernameLength'));
            $aUser['email'] = $oFaker->freeEmail;
            $aUser['first_name'] = $oFaker->firstName;
            $aUser['last_name'] = $oFaker->lastName;
            $aUser['password'] = $oFaker->password;
            $aUser['sex'] = $sSex;
            $aUser['match_sex'] = [$sMatchSex];
            $aUser['country'] = $oFaker->countryCode;
            $aUser['city'] = $oFaker->city;
            $aUser['address'] = $oFaker->streetAddress;
            $aUser['zip_code'] = $oFaker->postcode;
            $aUser['birth_date'] = $sBirthDate;
            $aUser['description'] = $oFaker->paragraph(2);
            $aUser['lang'] = $oFaker->locale;
            $aUser['website'] = Kernel::SOFTWARE_WEBSITE;
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);
        }
    }

    public function generateAffiliates()
    {
        $oAffModel = new AffiliateCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement([GenderTypeUserCore::MALE, GenderTypeUserCore::FEMALE]);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');
            $sWebsite = $oFaker->randomElement(
                [
                    Kernel::SOFTWARE_WEBSITE,
                    'http://pierrehenry.be',
                    'https://lifyzer.com'
                ]
            );

            $aUser = [];
            $aUser['username'] = Cleanup::username($oFaker->userName, DbConfig::getSetting('maxUsernameLength'));
            $aUser['email'] = $oFaker->email;
            $aUser['first_name'] = $oFaker->firstName;
            $aUser['last_name'] = $oFaker->lastName;
            $aUser['password'] = $oFaker->password;
            $aUser['sex'] = $sSex;
            $aUser['country'] = $oFaker->countryCode;
            $aUser['city'] = $oFaker->city;
            $aUser['address'] = $oFaker->streetAddress;
            $aUser['zip_code'] = $oFaker->postcode;
            $aUser['birth_date'] = $sBirthDate;
            $aUser['description'] = $oFaker->paragraph(2);
            $aUser['website'] = $sWebsite;
            $aUser['bank_account'] = $oFaker->bankAccountNumber;
            $aUser['lang'] = $oFaker->locale;
            $aUser['ip'] = $oFaker->ipv4;

            $oAffModel->add($aUser);
        }
    }

    public function generateSubscribers()
    {
        $oSubscriberModel = new SubscriberCoreModel();

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);
            $iAccountStatus = $oFaker->randomElement(
                [
                    SubscriberCoreModel::ACTIVE_STATUS,
                    SubscriberCoreModel::INACTIVE_STATUS
                ]
            );

            $aUser = [];
            $aUser['name'] = $oFaker->name;
            $aUser['email'] = $oFaker->email;
            $aUser['active'] = $iAccountStatus;
            $aUser['current_date'] = $oFaker->dateTime()->format('Y-m-d H:i:s');
            $aUser['hash_validation'] = Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH);
            $aUser['affiliated_id'] = 0;
            $aUser['ip'] = $oFaker->ipv4;

            $oSubscriberModel->add($aUser);
        }
    }
}
