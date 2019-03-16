<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Util\Various;

class FakerFactory
{
    /** @var int */
    private $iAmount;

    /**
     * @param int $iAmount Number of profile to generate.
     */
    public function __construct($iAmount)
    {
        $this->iAmount = $iAmount;
    }

    public function generateBuyers()
    {
        $oAffModel = new AffiliateCoreModel;

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
            $aUser['lang'] = $oFaker->locale;
            $aUser['ip'] = $oFaker->ipv4;

            $oAffModel->add($aUser);
        }
    }

    public function generateSellers()
    {
        $oUserModel = new UserCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create($this->sLocale);

            $sSex = $oFaker->randomElement(['male', 'female', 'couple']);
            $sMatchSex = $oFaker->randomElement(['male', 'female', 'couple']);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['username'] = $oFaker->userName;
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
}
