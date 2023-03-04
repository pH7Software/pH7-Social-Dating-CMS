<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

use Faker\Factory as Faker;
use PH7\Framework\Core\Kernel;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Util\Various;

class FakerFactory
{
    private int $iAmount;

    private string $sSex;

    private string $sLocale;

    /**
     * @param int $iAmount Number of profile to generate.
     * @param string $sSex Profile gender.
     * @param string $sLocale The locale if specified. e.g., en_US, en_IE, fr_FR, fr_BE, nl_NL, es_ES, ...
     */
    public function __construct(int $iAmount, string $sSex, string $sLocale = Lang::DEFAULT_LOCALE)
    {
        $this->iAmount = $iAmount;
        $this->sSex = $sSex;
        $this->sLocale = $sLocale;
    }

    public function generateMembers(): void
    {
        $oUserModel = new UserCoreModel;
        $oFaker = Faker::create($this->sLocale);

        $iMaxAge = DbConfig::getSetting('maxAgeRegistration');
        $iMinAge = DbConfig::getSetting('minAgeRegistration');
        $iMaxUsernameLength = (int)DbConfig::getSetting('maxUsernameLength');

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $sEmail = $oFaker->freeEmail;
            $sUsername = Cleanup::username($oFaker->userName, $iMaxUsernameLength);
            if ($this->isValidProfile($sEmail, $sUsername)) {
                $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
                $sMatchSex = $oFaker->randomElement(
                    [
                        GenderTypeUserCore::MALE,
                        GenderTypeUserCore::FEMALE,
                        GenderTypeUserCore::COUPLE
                    ]
                );
                $sBirthDate = $oFaker->dateTimeBetween(
                    sprintf('-%s years', $iMaxAge),
                    sprintf('-%s years', $iMinAge)
                )->format('Y-m-d');

                $aUser = [];
                $aUser['username'] = $sUsername;
                $aUser['email'] = $sEmail;
                $aUser['first_name'] = $oFaker->firstName($sSex);
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
                $aUser['ip'] = $oFaker->ipv4;

                $oUserModel->add($aUser);
            }
        }
    }

    public function generateAffiliates(): void
    {
        $oAffModel = new AffiliateCoreModel;
        $oFaker = Faker::create($this->sLocale);

        $iMaxUsernameLength = (int)DbConfig::getSetting('maxUsernameLength');

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $sEmail = $oFaker->email;
            $sUsername = Cleanup::username($oFaker->userName, $iMaxUsernameLength);
            if ($this->isValidProfile($sEmail, $sUsername)) {
                $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
                $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');
                $sWebsite = $oFaker->randomElement(
                    [
                        Kernel::SOFTWARE_WEBSITE,
                        'https://pierrehenry.be',
                        'https://lifyzer.com'
                    ]
                );

                $aUser = [];
                $aUser['username'] = $sUsername;
                $aUser['email'] = $sEmail;
                $aUser['first_name'] = $oFaker->firstName($sSex);
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
                $aUser['bank_account'] = $oFaker->companyEmail;
                $aUser['lang'] = $oFaker->locale;
                $aUser['ip'] = $oFaker->ipv4;

                $oAffModel->add($aUser);
            }
        }
    }

    public function generateSubscribers(): void
    {
        $oSubscriberModel = new SubscriberCoreModel;
        $oFaker = Faker::create($this->sLocale);

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
            $iAccountStatus = $oFaker->randomElement(
                [
                    SubscriberCoreModel::ACTIVE_STATUS,
                    SubscriberCoreModel::INACTIVE_STATUS
                ]
            );

            $aUser = [];
            $aUser['name'] = $oFaker->name($sSex);
            $aUser['email'] = $oFaker->email;
            $aUser['active'] = $iAccountStatus;
            $aUser['current_date'] = $oFaker->dateTime()->format('Y-m-d H:i:s');
            $aUser['hash_validation'] = Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH);
            $aUser['affiliated_id'] = 0;
            $aUser['ip'] = $oFaker->ipv4;

            $oSubscriberModel->add($aUser);
        }
    }

    /**
     * Returns random and correct genders for Faker's profiles (without 'couple' gender).
     *
     * @return string Gives 'male' or 'female' randomly.
     */
    private function getRandomGender(): string
    {
        $aGenders = [GenderTypeUserCore::MALE, GenderTypeUserCore::FEMALE];

        return $aGenders[array_rand($aGenders)];
    }

    private function isValidProfile(string $sEmail, string $sUsername): bool
    {
        $oExistsModel = new ExistCoreModel;
        $oValidate = new Validate;

        return $oValidate->email($sEmail) &&
            !$oExistsModel->email($sEmail) &&
            $oValidate->username($sUsername);
    }
}
