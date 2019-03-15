<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Layout\Tpl\Engine\Templatable;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;

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
    public function __construct($iAmount, $sLocale = '')
    {
        $this->iAmount = $iAmount;
        $this->sLocale = $sLocale;
    }

    public function generateMembers()
    {
        $oUserModel = new UserCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement(['male', 'female', 'couple']);
            $sMatchSex = $oFaker->randomElement(['male', 'female', 'couple']);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = $oFaker->userName;
            $aUser['email'] = $oFaker->email;
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
            $aUser['website'] = 'https://ph7cms.com';
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);
        }
    }

    public function generateAffiliates()
    {
        $oUserModel = new AffiliateCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement(['male', 'female']);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = $oFaker->userName;
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
            $aUser['website'] = 'http://pierrehenry.be';
            $aUser['phone'] = $oFaker->phoneNumber;
            $aUser['bank_account'] = $oFaker->bankAccountNumber;
            $aUser['lang'] = $oFaker->locale;
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);
        }
    }

    public function generateSubscribers()
    {
        $oUserModel = new Subscrib;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement(['male', 'female']);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = $oFaker->userName;
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
            $aUser['website'] = 'http://pierrehenry.be';
            $aUser['phone'] = $oFaker->phoneNumber;
            $aUser['bank_account'] = $oFaker->bankAccountNumber;
            $aUser['lang'] = $oFaker->locale;
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);
        }
    }
}
