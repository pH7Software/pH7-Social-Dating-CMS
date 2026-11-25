<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Util\Various;
use Xefi\Faker\Extensions\PersonExtension;
use Xefi\Faker\Faker;

class FakerFactory
{
    public const SUPPORTED_LOCALES = [
        'en_US' => 'American',
        'en_AU' => 'Australian',
        'pt_BR' => 'Brazilian',
        'en_GB' => 'British',
        'cs_CZ' => 'Czech',
        'da_DK' => 'Danish',
        'nl_NL' => 'Dutch',
        'nl_BE' => 'Dutch Belgian',
        'en_CA' => 'English Canadian',
        'fr_CA' => 'French Canadian',
        'fi_FI' => 'Finnish',
        'fr_FR' => 'French',
        'fr_BE' => 'French Belgian',
        'fr_CH' => 'French Swiss',
        'de_DE' => 'German',
        'de_CH' => 'German Swiss',
        'fa_IR' => 'Iranian',
        'en_IE' => 'Irish',
        'it_IT' => 'Italian',
        'it_CH' => 'Italian Swiss',
        'en_NZ' => 'New Zealander',
        'nb_NO' => 'Norwegian',
        'es_PE' => 'Peruvian',
        'pt_PT' => 'Portuguese',
        'ro_RO' => 'Romanian',
        'ru_RU' => 'Russian',
        'es_ES' => 'Spanish',
        'tr_TR' => 'Turkish',
        'es_VE' => 'Venezuelan'
    ];

    private int $iAmount;

    private string $sSex;

    private string $sLocale;

    /**
     * @param int    $iAmount number of profile to generate
     * @param string $sSex    profile gender
     * @param string $sLocale The locale if specified. e.g., en_US, en_IE, fr_FR, fr_BE, nl_NL, es_ES, ...
     */
    public function __construct(int $iAmount, string $sSex, string $sLocale = Lang::DEFAULT_LOCALE)
    {
        if (!self::isSupportedLocale($sLocale)) {
            throw new \InvalidArgumentException('The requested Faker locale is not supported.');
        }

        $this->iAmount = $iAmount;
        $this->sSex = $sSex;
        $this->sLocale = $sLocale;
    }

    public static function isSupportedLocale(string $sLocale): bool
    {
        return array_key_exists($sLocale, self::SUPPORTED_LOCALES);
    }

    public function generateMembers(): void
    {
        $oUserModel = new UserCoreModel();
        $oFaker = FakerGeneratorFactory::create($this->sLocale);

        $iMaxAge = DbConfig::getSetting('maxAgeRegistration');
        $iMinAge = DbConfig::getSetting('minAgeRegistration');
        $iMaxUsernameLength = (int)DbConfig::getSetting('maxUsernameLength');

        for ($iProfile = 1; $iProfile <= $this->iAmount; ++$iProfile) {
            $sEmail = $this->getSafeEmail($oFaker);
            $sUsername = Cleanup::username($oFaker->sdl(), $iMaxUsernameLength);
            if ($this->isValidProfile($sEmail, $sUsername)) {
                $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
                $sMatchSex = $oFaker->randomElement(
                    [
                        GenderTypeUserCore::MALE,
                        GenderTypeUserCore::FEMALE,
                        GenderTypeUserCore::COUPLE
                    ]
                );
                $sBirthDate = $oFaker->dateTime(
                    sprintf('-%s years', $iMaxAge),
                    sprintf('-%s years', $iMinAge)
                )->format('Y-m-d');

                $aUser = [];
                $aUser['username'] = $sUsername;
                $aUser['email'] = $sEmail;
                $aUser['first_name'] = $oFaker->firstName($this->getFakerGender($sSex));
                $aUser['last_name'] = $oFaker->lastName();
                $aUser['password'] = $this->getPassword();
                $aUser['sex'] = $sSex;
                $aUser['match_sex'] = [$sMatchSex];
                $aUser['country'] = $this->getCountryCode();
                $aUser['city'] = $oFaker->city();
                $aUser['address'] = $oFaker->streetAddress();
                $aUser['zip_code'] = $this->getPostcode($oFaker);
                $aUser['birth_date'] = $sBirthDate;
                $aUser['description'] = $oFaker->paragraphs(2);
                $aUser['lang'] = $this->sLocale;
                $aUser['ip'] = $oFaker->ipv4();

                $oUserModel->add($aUser);
            }
        }
    }

    public function generateAffiliates(): void
    {
        $oAffModel = new AffiliateCoreModel();
        $oFaker = FakerGeneratorFactory::create($this->sLocale);

        $iMaxUsernameLength = (int)DbConfig::getSetting('maxUsernameLength');

        for ($iProfile = 1; $iProfile <= $this->iAmount; ++$iProfile) {
            $sEmail = $this->getSafeEmail($oFaker);
            $sUsername = Cleanup::username($oFaker->sdl(), $iMaxUsernameLength);
            if ($this->isValidProfile($sEmail, $sUsername)) {
                $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
                $sBirthDate = $oFaker->dateTime('-65 years', '-18 years')->format('Y-m-d');
                $sWebsite = $oFaker->randomElement(
                    [
                        Kernel::SOFTWARE_WEBSITE,
                        'https://pierrehenry.be',
                        'https://mealsnap.ph7.me'
                    ]
                );

                $aUser = [];
                $aUser['username'] = $sUsername;
                $aUser['email'] = $sEmail;
                $aUser['first_name'] = $oFaker->firstName($this->getFakerGender($sSex));
                $aUser['last_name'] = $oFaker->lastName();
                $aUser['password'] = $this->getPassword();
                $aUser['sex'] = $sSex;
                $aUser['country'] = $this->getCountryCode();
                $aUser['city'] = $oFaker->city();
                $aUser['address'] = $oFaker->streetAddress();
                $aUser['zip_code'] = $this->getPostcode($oFaker);
                $aUser['birth_date'] = $sBirthDate;
                $aUser['description'] = $oFaker->paragraphs(2);
                $aUser['website'] = $sWebsite;
                $aUser['bank_account'] = $this->getSafeEmail($oFaker);
                $aUser['lang'] = $this->sLocale;
                $aUser['ip'] = $oFaker->ipv4();

                $oAffModel->add($aUser);
            }
        }
    }

    public function generateSubscribers(): void
    {
        $oSubscriberModel = new SubscriberCoreModel();
        $oFaker = FakerGeneratorFactory::create($this->sLocale);

        for ($iProfile = 1; $iProfile <= $this->iAmount; ++$iProfile) {
            $sSex = empty($this->sSex) ? $this->getRandomGender() : $this->sSex;
            $iAccountStatus = $oFaker->randomElement(
                [
                    SubscriberCoreModel::ACTIVE_STATUS,
                    SubscriberCoreModel::INACTIVE_STATUS
                ]
            );

            $aUser = [];
            $aUser['name'] = $oFaker->name($this->getFakerGender($sSex));
            $aUser['email'] = $this->getSafeEmail($oFaker);
            $aUser['active'] = $iAccountStatus;
            $aUser['current_date'] = $oFaker->dateTime()->format('Y-m-d H:i:s');
            $aUser['hash_validation'] = Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH);
            $aUser['affiliated_id'] = 0;
            $aUser['ip'] = $oFaker->ipv4();

            $oSubscriberModel->add($aUser);
        }
    }

    /**
     * Returns random and correct genders for Faker's profiles (without 'couple' gender).
     *
     * @return string gives 'male' or 'female' randomly
     */
    private function getRandomGender(): string
    {
        $aGenders = [GenderTypeUserCore::MALE, GenderTypeUserCore::FEMALE];

        return $aGenders[array_rand($aGenders)];
    }

    private function getSafeEmail(Faker $oFaker): string
    {
        return $oFaker->sdl() . '@example.com';
    }

    private function getPassword(): string
    {
        return bin2hex(random_bytes(12));
    }

    private function getFakerGender(string $sSex): ?string
    {
        return match ($sSex) {
            GenderTypeUserCore::MALE => PersonExtension::GENDER_MALE,
            GenderTypeUserCore::FEMALE => PersonExtension::GENDER_FEMALE,
            default => null
        };
    }

    private function getCountryCode(): string
    {
        return substr($this->sLocale, 3, 2);
    }

    private function getPostcode(Faker $oFaker): string
    {
        $mPostcode = in_array($this->sLocale, ['en_US', 'de_DE'], true)
            ? $oFaker->zipCode()
            : $oFaker->postcode();

        return (string)$mPostcode;
    }

    private function isValidProfile(string $sEmail, string $sUsername): bool
    {
        $oExistsModel = new ExistCoreModel();
        $oValidate = new Validate();

        return $oValidate->email($sEmail)
            && !$oExistsModel->email($sEmail)
            && $oValidate->username($sUsername);
    }
}
