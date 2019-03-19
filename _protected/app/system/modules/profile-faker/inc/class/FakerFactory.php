<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Inc / Class
 */

namespace PH7;

class FakerFactory
{
    const PROFILE_LOCALE = 'en_US';

    const UTAH_CITIES = [
        '' => '--Select City--',
        'Alpine',
        'American Fork',
        'Bountiful',
        'Brigham City',
        'Canyon Rim',
        'Cedar City',
        'Centerville',
        'Clearfield',
        'Clinton',
        'Cottonwood Heights',
        'Cottonwood West',
        'Draper',
        'East Millcreek',
        'Farmington',
        'Grantsville',
        'Heber',
        'Highland',
        'Holladay',
        'Hurricane',
        'Hyrum',
        'Kaysville',
        'Kearns',
        'Layton',
        'Lehi',
        'Lindon',
        'Little Cottonwood Creek Valley',
        'Logan',
        'Magna',
        'Midvale',
        'Millcreek',
        'Mount Olympus',
        'Murray',
        'North Logan',
        'North Ogden',
        'North Salt Lake',
        'Ogden',
        'Oquirrh',
        'Orem',
        'Park City',
        'Payson',
        'Pleasant Grove',
        'Price',
        'Provo',
        'Richfield',
        'Riverdale',
        'Riverton',
        'Roy',
        'Salt Lake City',
        'Sandy',
        'Smithfield',
        'South Jordan',
        'South Ogden',
        'South Salt Lake',
        'Spanish Fork',
        'Springville',
        'St. George',
        'Summit Park',
        'Syracuse',
        'Taylorsville',
        'Tooele',
        'Vernal',
        'Washington',
        'Washington Terrace',
        'West Jordan',
        'West Point',
        'West Valley City',
        'Woods Cross'
    ];

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
            $oFaker = \Faker\Factory::create(self::PROFILE_LOCALE);

            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['sex'] = 'buyer';
            $aUser['username'] = $oFaker->userName;
            $aUser['email'] = $oFaker->email;
            $aUser['first_name'] = $oFaker->firstName;
            $aUser['last_name'] = $oFaker->lastName;
            $aUser['password'] = $oFaker->password;
            $aUser['phone'] = $oFaker->phoneNumber;
            $aUser['country'] = $oFaker->countryCode;
            $aUser['city'] = $oFaker->city;
            $aUser['address'] = $oFaker->streetAddress;
            $aUser['zip_code'] = $oFaker->postcode;
            $aUser['birth_date'] = $sBirthDate;
            $aUser['description'] = $oFaker->paragraph(2);
            $aUser['ip'] = $oFaker->ipv4;

            $oAffModel->add($aUser);
        }
    }

    public function generateSellers()
    {
        $oUserModel = new UserCoreModel;

        for ($iProfile = 1; $iProfile <= $this->iAmount; $iProfile++) {
            $oFaker = \Faker\Factory::create(self::PROFILE_LOCALE);

            $sCity = $oFaker->randomElement(self::UTAH_CITIES);
            $sBirthDate = $oFaker->dateTimeBetween('-65 years', '-18 years')->format('Y-m-d');

            $aUser = [];
            $aUser['sex'] = 'seller';
            $aUser['username'] = $oFaker->userName;
            $aUser['email'] = $oFaker->freeEmail;
            $aUser['first_name'] = $oFaker->firstName;
            $aUser['last_name'] = $oFaker->lastName;
            $aUser['password'] = $oFaker->password;
            $aUser['country'] = $oFaker->countryCode;
            $aUser['city'] = $sCity;
            $aUser['address'] = $oFaker->streetAddress;
            $aUser['birth_date'] = $sBirthDate;
            $aUser['description'] = $oFaker->paragraph(3);
            $aUser['phone'] = $oFaker->phoneNumber;
            $aUser['property_price'] = $oFaker->numberBetween([900, 99999999]);
            $aUser['property_bedrooms'] = $oFaker->randomElement([1, 2, 3, 4, 5]);
            $aUser['property_bathrooms'] = $oFaker->randomElement([1, 2, 3, 4]);
            $aUser['property_year_built'] = $oFaker->year('-1 years');
            $aUser['property_home_type'] = $oFaker->randomElement(['family', 'condo']);
            $aUser['property_home_style'] = $oFaker->randomElement(['rambler', 'ranch', 'tri-multi-level', 'two-story', 'other']);
            $aUser['property_garage_spaces'] = $oFaker->randomElement([0, 1, 2, 3, 4]);
            $aUser['property_carport_spaces'] = $oFaker->randomElement([0, 1, 2]);
            $aUser['contact_times'] = $oFaker->randomElement(['morning', 'afternoon', 'evening']);
            $aUser['ip'] = $oFaker->ipv4;

            $oUserModel->add($aUser);
        }
    }
}
