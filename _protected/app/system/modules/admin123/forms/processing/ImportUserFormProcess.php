<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
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

    const ERR_BAD_FILE = 1, ERR_TOO_LARGE = 2, ERR_INVALID = 3;

    private $_aFile, $_aData, $_aTmpData, $_aFileData, $_iErrType;

    public function __construct()
    {
        parent::__construct();

        $this->_aFile = $_FILES['csv_file'];
        $sExtFile = $this->file->getFileExt($this->_aFile['name']);
        $sDelimiter = $this->httpRequest->post('delimiter');
        $sEnDelimiter = $this->httpRequest->post('enclosure');

        if ($sExtFile != 'csv' && $sExtFile != 'txt')
            $this->_iErrType = static::ERR_BAD_FILE;
        elseif ($this->_aFile['error'] == UPLOAD_ERR_INI_SIZE)
            $this->_iErrType = static::ERR_TOO_LARGE;
        elseif (!$rHandler = @fopen($this->_aFile['tmp_name'], 'rb'))
            $this->_iErrType = static::ERR_INVALID;
        elseif (!($this->_aFileData = @fgetcsv($rHandler, 0, $sDelimiter, $sEnDelimiter)) || !is_array($this->_aFileData))
            $this->_iErrType = static::ERR_INVALID;

        if (!empty($this->_iErrType))
        {
            $this->_removeTmpFile();

            \PFBC\Form::setError('form_import_user', $this->getErrMsg());
            return; // Stop execution of the method.
        }

        /**
         * Default value...
         */
        $aGenderList = ['male', 'female', 'couple'];
        $sFiveChars = Various::genRnd($this->_aFile['name'], 5);

        $this->_aTmpData = [
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

        foreach ($this->_aFileData as $sKey => $sVal)
        {
            // Clean the text to make comparisons easier...
            $sVal = strtolower(trim(str_replace(array('-', '_', ' '), '', $sVal)));

            // Test comparisons of strings and adding values in an array "$this->_aTmpData"
            if ($sVal == 'username' || $sVal == 'login' || $sVal == 'user' || $sVal == 'nickname') $this->_aTmpData['username'] = $sKey;
            if ($sVal == 'name' || $sVal == 'firstname' || $sVal == 'forname') $this->_aTmpData['first_name'] = $sKey;
            if ($sVal == 'lastname' || $sVal == 'surname') $this->_aTmpData['last_name'] = $sKey;
            if ($sVal == 'matchsex' || $sVal == 'looking' || $sVal == 'lookingfor') $this->_aTmpData['match_sex'] = $sKey;
            if ($sVal == 'sex' || $sVal == 'gender') $this->_aTmpData['sex'] = $sKey;
            if ($sVal == 'email' || $sVal == 'mail') $this->_aTmpData['email'] = $sKey;
            if ($sVal == 'desc' || $sVal == 'description' || $sVal == 'descriptionme' || $sVal == 'generaldescription' || $sVal == 'about' || $sVal == 'aboutme' || $sVal == 'bio' || $sVal == 'biography' || $sVal == 'comment') $this->_aTmpData['description'] = $sKey;
            if ($sVal == 'country' || $sVal == 'countryid') $this->_aTmpData['country'] = $sKey;
            if ($sVal == 'city' || $sVal == 'town') $this->_aTmpData['city'] = $sKey;
            if ($sVal == 'state' || $sVal == 'district' || $sVal == 'province' || $sVal == 'region') $this->_aTmpData['state'] = $sKey;
            if ($sVal == 'zip' || $sVal == 'zipcode' || $sVal == 'postal' || $sVal == 'postalcode' || $sVal == 'eircode') $this->_aTmpData['zip_code'] = $sKey;
            if ($sVal == 'website' || $sVal == 'site' || $sVal == 'url') $this->_aTmpData['website'] = $sKey;
            if ($sVal == 'birthday' || $sVal == 'birthdate' || $sVal == 'dateofbirth') $this->_aTmpData['birth_date'] = $this->dateTime->get($sKey)->date('Y-m-d');
        }

        $iRow = 0;
        $oUserModel = new UserCoreModel;
        $oExistsModel = new ExistsCoreModel;
        $oValidate = new Validate;
        while (($this->_aFileData = fgetcsv($rHandler, 0, $sDelimiter, $sEnDelimiter)) !== false)
        {
            $sEmail = trim($this->_aFileData[$this->_aTmpData['email']]);
            if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail))
            {
                $aTypes = [
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'password',
                    'sex',
                    'match_sex',
                    'birth_date',
                    'description',
                    'country',
                    'city',
                    'state',
                    'zip_code',
                    'website',
                    'social_network_site',
                    'ip'
                ];

                $this->setData($aTypes, $iRow);

                $oUserModel->add(escape($this->_aData[$iRow], true));
                $iRow++;
            }
        }

        $this->_removeTmpFile();
        unset($oUserModel, $oExistsModel, $oValidate, $this->_aTmpData, $this->_aFileData, $this->_aData);
        fclose($rHandler);

        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'user', 'browse'), nt('%n% user has been successfully added.', '%n% users has been successfully added.', $iRow));
    }

    /**
     * Returns the error message for the form.
     *
     * @return string The error message.
     */
    protected function getErrMsg()
    {
        switch ($this->_iErrType)
        {
            case static::ERR_BAD_FILE:
                $sErrMsg = t('Invalid File Format! Please select a valid CSV/TXT file containing data members.');
            break;

            case static::ERR_TOO_LARGE:
                $sErrMsg = t('The file is too large. Please select a smaller file or change your server PHP settings. Especially "upload_max_filesize" and "post_max_size" directives in the php.ini file.');
            break;

            case static::ERR_INVALID:
                $sErrMsg = t('The file is Invalid/empty or incorrect Delimiter/Enclosure set.');
            break;
        }

        return $sErrMsg;
    }

    /**
     * Check and set the data from the CSV file.
     *
     * @return void
     */
    protected function setData(array $aType, $iRow)
    {
        $oUser = new UserCore;

        foreach ($aType as $sType)
        {
            $sData = (!empty($this->_aFileData[$this->_aTmpData[$sType]])) ? trim($this->_aFileData[$this->_aTmpData[$sType]]) : $this->_aTmpData[$sType];

            if ($sType == 'username') {
                $oUser->findUsername($sData, $this->_aData[$iRow]['first_name'], $this->_aData[$iRow]['last_name']);
            } elseif ($sType == 'match_sex') {
                $this->_aData[$iRow][$sType] = array($sData);
            } else {
                $this->_aData[$iRow][$sType] = $sData;
            }
        }

        unset($oUser);
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
