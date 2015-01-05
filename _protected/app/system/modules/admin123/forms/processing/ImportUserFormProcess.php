<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From / Processing
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Util\Various,
PH7\Framework\Security\Validate\Validate,
PH7\Framework\Ip\Ip,
PH7\Framework\Mvc\Router\Uri,
PH7\Framework\Url\Header;

class ImportUserFormProcess extends Form
{

    const ERR_BAD_FILE = 'bad_file';

    private $_aFile;

    public function __construct()
    {
        parent::__construct();

        $this->_aFile = $_FILES['csv_file'];
        $sExtFile = $this->file->getFileExt($this->_aFile['name']);
        $sDelimiter = $this->httpRequest->post('delimiter');
        $sEnDelimiter = $this->httpRequest->post('enclosure');

        if ($sExtFile != 'csv' && $sExtFile != 'txt')
            $sErrMsg = static::ERR_BAD_FILE;

        elseif (!$rHandler = @fopen($this->_aFile['tmp_name'], 'rb'))
            $sErrMsg = static::ERR_BAD_FILE;

         elseif (!($aFileData = @fgetcsv($rHandler, 0, $sDelimiter, $sEnDelimiter)) || !(is_array($aFileData)))
             $sErrMsg = static::ERR_BAD_FILE;


         if (!empty($sErrMsg) && $sErrMsg == static::ERR_BAD_FILE)
         {
            $this->_removeTmpFile();
            \PFBC\Form::setError('form_import_user', t('Wrong file! Please select a valid CSV file containing data members.'));
            return; // Stop execution of the method.
        }


        /**
         * Default value...
         */
        $aGenderList = ['male', 'female', 'couple'];
        $sFiveChars = Various::genRnd($this->_aFile['name'], 5);

        $aTmpData = [
            'email' => 'pierrehenrysoriasanz' . $sFiveChars . '@hizup' . $sFiveChars . '.com',
            'username' => 'Hizup' . $sFiveChars,
            'password' => Various::genRnd(),
            'first_name' => 'Alex' . $sFiveChars,
            'last_name' => 'Rolli' . $sFiveChars,
            'sex' => $aGenderList[mt_rand(0,2)], // Generate randomly it
            'match_sex' => $aGenderList[mt_rand(0,2)], // Generate randomly it
            'birth_date' => date('Y')-mt_rand(20,40).'-'.mt_rand(1,12).'-'.mt_rand(1,28), // Generate randomly the anniversary date
            'country' => 'US',
            'city' => 'Virginia',
            'state' => 'Doswell',
            'zip_code' => '23047',
            'description' => 'Hi all!<br />How are you today?<br /> Bye ;)',
            'website' => '',
            'social_network_site' => '',
            'ip' => Ip::get()
        ];

        foreach ($aFileData as $sKey => $sVal)
        {
            // Clean the text to make comparisons easier...
            $sVal = strtolower(trim(str_replace(array('-', '_', ' '), '', $sVal)));

            // Test comparisons of strings and adding values in an array "$aTmpData"
            if (($sVal == 'username') || ($sVal == 'login') || ($sVal == 'user') || ($sVal == 'nickname')) $aTmpData['username'] = $sKey;
            if (($sVal == 'name') || ($sVal == 'firstname')) $aTmpData['first_name'] = $sKey;
            if (($sVal == 'lastname') || ($sVal == 'surname')) $aTmpData['last_name'] = $sKey;
            if (($sVal == 'matchsex') || ($sVal == 'looking') || ($sVal == 'lookingfor')) $aTmpData['match_sex'] = $sKey;
            if (($sVal == 'sex') || ($sVal == 'gender')) $aTmpData['sex'] = $sKey;
            if (($sVal == 'email') || ($sVal == 'mail')) $aTmpData['email'] = $sKey;
            if (($sVal == 'desc') || ($sVal == 'description') || ($sVal == 'descriptionme') || ($sVal == 'generaldescription') || ($sVal == 'about') || ($sVal == 'aboutme') || ($sVal == 'bio') || ($sVal == 'biography') || ($sVal == 'comment')) $aTmpData['description'] = $sKey;
            if (($sVal == 'country') || ($sVal == 'countryid')) $aTmpData['country'] = $sKey;
            if (($sVal == 'city') || ($sVal == 'town')) $aTmpData['city'] = $sKey;
            if (($sVal == 'state') || ($sVal == 'district') || ($sVal == 'province') || ($sVal == 'region')) $aTmpData['state'] = $sKey;
            if (($sVal == 'zip') || ($sVal == 'zipcode') || ($sVal == 'postal') || ($sVal == 'postalcode')) $aTmpData['zip_code'] = $sKey;
            if (($sVal == 'website') || ($sVal == 'site') || $sVal == 'url') $aTmpData['website'] = $sKey;
            if (($sVal == 'birthday') || ($sVal == 'birthdate') || ($sVal == 'dateofbirth')) $aTmpData['birth_date'] = $this->dateTime->get($sKey)->date('Y-m-d');
        }

        $iRow = 0;
        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $oExistsModel = new ExistsCoreModel;
        $oValidate = new Validate;
        while (($aFileData = fgetcsv($rHandler, 0, $sDelimiter, $sEnDelimiter)) !== false)
        {
            $aData[$iRow] = $aTmpData; // Set data by the default contents

            $sEmail = trim($aFileData[$aTmpData['email']]);
            if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail))
            {
                $sUsername = trim($aFileData[$aTmpData['username']]);
                $sFirstName = trim($aFileData[$aTmpData['first_name']]);
                $sLastName = trim($aFileData[$aTmpData['last_name']]);

                $aData[$iRow]['username'] = $oUser->findUsername($sUsername, $sFirstName, $sLastName);
                $aData[$iRow]['first_name'] = $sFirstName;
                $aData[$iRow]['last_name'] = $sLastName;
                $aData[$iRow]['sex'] = trim($aFileData[$aTmpData['sex']]);
                $aData[$iRow]['match_sex'] = array(trim($aFileData[$aTmpData['match_sex']]));
                $aData[$iRow]['email'] = $sEmail;
                $aData[$iRow]['description'] = trim($aFileData[$aTmpData['description']]);
                $aData[$iRow]['country'] = trim($aFileData[$aTmpData['country']]);
                $aData[$iRow]['city'] = trim($aFileData[$aTmpData['city']]);
                $aData[$iRow]['state'] = trim($aFileData[$aTmpData['state']]);
                $aData[$iRow]['zip_code'] = trim($aFileData[$aTmpData['zip_code']]);
                $aData[$iRow]['website'] = trim($aFileData[$aTmpData['website']]);
                $aData[$iRow]['birth_date'] = trim($aFileData[$aTmpData['birth_date']]);

                $oUserModel->add(escape($aData[$iRow], true));
                $iRow++;
            }
        }

        $this->_removeTmpFile();
        unset($oUser, $oUserModel, $oExistsModel, $oValidate, $aTmpData, $aData);
        fclose($rHandler);

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'user', 'browse'), nt('%n% User has been successfully added.', '%n% Users has been successfully added.', $iRow));
    }

    /**
     * Remove the temporary file.
     *
     * @return void
     */
    private function _removeTmpFile()
    {
        $this->file->deleteFile($this->_aFile['tmp_name']);
    }

}
