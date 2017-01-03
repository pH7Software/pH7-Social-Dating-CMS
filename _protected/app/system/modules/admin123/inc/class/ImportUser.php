<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use
PH7\Framework\Util\Various,
PH7\Framework\Security\Validate\Validate,
PH7\Framework\Ip\Ip;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class ImportUser extends Core
{

    const ERR_BAD_FILE = 1, ERR_TOO_LARGE = 2, ERR_INVALID = 3;

    private $_aFile, $_aData = [], $_aTmpData, $_aFileData, $_aRes, $_iErrType;

    /**
     * @var array $_aGenderList Gender types available for pH7CMS.
     */
    private $_aGenderList = ['male', 'female', 'couple'];

    /*
     * @var array $_aDbTypes Array containing the DB data types.
     */
    private $_aDbTypes = [
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

    /**
     * @param array $aFiles
     * @param string $sDelimiter Delimiter Field delimiter (one character).
     * @param string $sEnclosure Enclosure Field enclosure (one character).
     * @return void
     */
    public function __construct(array $aFiles, $sDelimiter, $sEnclosure)
    {
        parent::__construct();

        $this->_aFile = $aFiles;
        $sExtFile = $this->file->getFileExt($this->_aFile['name']);

        if ($sExtFile != 'csv' && $sExtFile != 'txt')
            $this->_iErrType = static::ERR_BAD_FILE;
        elseif ($this->_aFile['error'] == UPLOAD_ERR_INI_SIZE)
            $this->_iErrType = static::ERR_TOO_LARGE;
        elseif (!$rHandler = @fopen($this->_aFile['tmp_name'], 'rb'))
            $this->_iErrType = static::ERR_INVALID;
        elseif (!($this->_aFileData = @fgetcsv($rHandler, 0, $sDelimiter, $sEnclosure)) || !is_array($this->_aFileData))
            $this->_iErrType = static::ERR_INVALID;

        if (!empty($this->_iErrType)) {
            $this->_removeTmpFile();
            $this->_aRes = ['status' => false, 'msg' => $this->getErrMsg()];
        } else {
            $this->setDefVals();

            foreach ($this->_aFileData as $sKey => $sVal) {
                // Clean the text to make comparisons easier...
                $sVal = strtolower(trim(str_replace(['-', '_', ' '], '', $sVal)));

                // Test comparisons of strings and adding values in an array "ImportUser::$_aTmpData"
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
                if ($sVal == 'birthday' || $sVal == 'birthdate' || $sVal == 'dateofbirth') $this->_aTmpData['birth_date'] = $sKey;
            }

            $iRow = 0;
            $oUserModel = new UserCoreModel;
            $oExistsModel = new ExistsCoreModel;
            $oValidate = new Validate;
            while (($this->_aFileData = fgetcsv($rHandler, 0, $sDelimiter, $sEnclosure)) !== false) {
                $sEmail = trim($this->_aFileData[$this->_aTmpData['email']]);
                if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail)) {
                    $this->setData($iRow);
                    $oUserModel->add(escape($this->_aData[$iRow], true));
                    $iRow++;
                }
            }

            $this->_removeTmpFile();
            fclose($rHandler);
            unset($rHandler, $oUserModel, $oExistsModel, $oValidate, $this->_aTmpData, $this->_aFileData, $this->_aData);

            $this->_aRes = ['status' => true, 'msg' => nt('%n% user has been successfully added.', '%n% users has been successfully added.', $iRow)];
        }
    }

    /**
     * @return array (boolean | string) ['status', 'msg']
     */
    public function getResponse()
    {
        return $this->_aRes;
    }

    /**
     * Check and set the data from the CSV file.
     *
     * @param integer $iRow Number of row of the CSV file
     * @return void
     */
    protected function setData($iRow)
    {
        $oUser = new UserCore;

        foreach ($this->_aDbTypes as $sType) {
            $sData = (!empty($this->_aFileData[$this->_aTmpData[$sType]])) ? trim($this->_aFileData[$this->_aTmpData[$sType]]) : $this->_aTmpData[$sType];

            if ($sType == 'username') {
                $this->_aData[$iRow][$sType] = $oUser->findUsername($sData, $this->_aData[$iRow]['first_name'], $this->_aData[$iRow]['last_name']);
            } elseif ($sType == 'sex') {
                $this->_aData[$iRow][$sType] = $this->checkGender($sData);
            } elseif ($sType == 'match_sex') {
                $this->_aData[$iRow][$sType] = [$this->checkGender($sData)];
            } elseif ($sType == 'birth_date') {
                $this->_aData[$iRow][$sType] = $this->dateTime->get($sData)->date('Y-m-d');
            } else {
                $this->_aData[$iRow][$sType] = $sData;
            }
        }

        unset($oUser);
    }

    /**
     * Set default values for the "ImportUser::$_aTmpData" array.
     *
     * @return void
     */
    protected function setDefVals()
    {
        $sFiveChars = Various::genRnd($this->_aFile['name'], 5);

        $this->_aTmpData = [
            'email' => 'pierrehenrysoriasanz' . $sFiveChars . '@hizup' . $sFiveChars . '.com',
            'username' => 'Hizup' . $sFiveChars,
            'password' => Various::genRnd(),
            'first_name' => 'Alex' . $sFiveChars,
            'last_name' => 'Rolli' . $sFiveChars,
            'sex' => $this->_aGenderList[mt_rand(0,2)], // Generate randomly it
            'match_sex' => $this->_aGenderList[mt_rand(0,2)], // Generate randomly it
            'birth_date' => date('Y')-mt_rand(20,50).'-'.mt_rand(1,12).'-'.mt_rand(1,28), // Generate randomly the anniversary date
            'country' => 'US',
            'city' => 'Virginia',
            'state' => 'Doswell',
            'zip_code' => '23047',
            'description' => 'Hi all!<br />How are you today?<br /> Bye ;)',
            'website' => '',
            'social_network_site' => '',
            'ip' => Ip::get()
        ];
    }

    /**
     * Returns the error message for the form.
     *
     * @return string The error message.
     */
    protected function getErrMsg()
    {
        switch ($this->_iErrType) {
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
     * Check (and modify if incorrect) the gender type.
     *
     * @param string $sSex
     * @return string
     */
    protected function checkGender($sSex)
    {
        $sSex = strtolower($sSex);

        if (!in_array($sSex, $this->_aGenderList))
            $sSex = $this->_aGenderList[mt_rand(0,2)];

        return $sSex;
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
