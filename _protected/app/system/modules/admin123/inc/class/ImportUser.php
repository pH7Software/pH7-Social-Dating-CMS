<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Inc / Class
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Ip\Ip;
use PH7\Framework\Security\Validate\Validate;
use PH7\Framework\Util\Various;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class ImportUser extends Core
{
    const NO_ERROR = 0;
    const ERR_BAD_FILE = 1;
    const ERR_TOO_LARGE = 2;
    const ERR_INVALID = 3;

    const IMPORT_FILE_EXTENSION = 'csv';

    /*
     * @var array Array containing the DB data types.
     */
    const DB_TYPES = [
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

    /** @var bool|resource */
    private $rHandler;

    /** @var array */
    private $aFile;

    /** @var array */
    private $aData = [];

    /** @var array */
    private $aTmpData;

    /** @var array */
    private $aFileData;

    /** @var array */
    private $aRes;

    /**
     * @var array $aGenderList Gender types available for pH7CMS.
     */
    private static $aGenderList = ['male', 'female', 'couple'];

    /**
     * @param array $aFile
     * @param string $sDelimiter Delimiter Field delimiter (one character).
     * @param string $sEnclosure Enclosure Field enclosure (one character).
     */
    public function __construct(array $aFile, $sDelimiter, $sEnclosure)
    {
        parent::__construct();

        // Initialize necessary attributes
        $this->aFile = $aFile;
        $this->rHandler = @fopen($this->aFile['tmp_name'], 'rb');
        $this->aFileData = @fgetcsv($this->rHandler, 0, $sDelimiter, $sEnclosure);
        $this->aRes = $this->run();
    }

    /**
     * @return array (boolean | string) ['status', 'msg']
     */
    public function getResponse()
    {
        return $this->aRes;
    }

    /**
     * Check and set the data from the CSV file.
     *
     * @param int $iRow Number of row of the CSV file
     *
     * @return void
     */
    private function setData($iRow)
    {
        $oUser = new UserCore;

        foreach (self::DB_TYPES as $sType) {
            $sData = !empty($this->aFileData[$this->aTmpData[$sType]]) ? trim($this->aFileData[$this->aTmpData[$sType]]) : $this->aTmpData[$sType];

            if ($sType === 'username') {
                $this->aData[$iRow][$sType] = $oUser->findUsername($sData, $this->aData[$iRow]['first_name'], $this->aData[$iRow]['last_name']);
            } elseif ($sType === 'sex') {
                $this->aData[$iRow][$sType] = $this->checkGender($sData);
            } elseif ($sType === 'match_sex') {
                $this->aData[$iRow][$sType] = [$this->checkGender($sData)];
            } elseif ($sType === 'birth_date') {
                $this->aData[$iRow][$sType] = $this->dateTime->get($sData)->date('Y-m-d');
            } else {
                $this->aData[$iRow][$sType] = $sData;
            }
        }

        unset($oUser);
    }

    /**
     * Set default values for the "ImportUser::$aTmpData" array.
     *
     * @return void
     */
    private function setDefVals()
    {
        $sFiveChars = Various::genRnd($this->aFile['name'], 5);

        $this->aTmpData = [
            'email' => 'pierrehenrysoriasanz' . $sFiveChars . '@ph7cms' . $sFiveChars . '.com',
            'username' => 'pH7CMS' . $sFiveChars,
            'password' => Various::genRnd(),
            'first_name' => 'Alex' . $sFiveChars,
            'last_name' => 'Rolli' . $sFiveChars,
            'sex' => self::$aGenderList[mt_rand(0, 2)], // Generate randomly it
            'match_sex' => self::$aGenderList[mt_rand(0, 2)], // Generate randomly it
            'birth_date' => date('Y') - mt_rand(20, 50) . '-' . mt_rand(1, 12) . '-' . mt_rand(1, 28), // Generate randomly the anniversary date
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

    private function setTmpData()
    {
        foreach ($this->aFileData as $sKey => $sVal) {
            $sVal = $this->cleanValue($sVal);

            // Test comparisons of strings and adding values in an array "ImportUser::$aTmpData"
            if ($sVal === 'username' || $sVal === 'login' || $sVal === 'user' || $sVal === 'nickname') {
                $this->aTmpData['username'] = $sKey;
            }

            if ($sVal === 'name' || $sVal === 'firstname' || $sVal === 'givenname' || $sVal === 'forname') {
                $this->aTmpData['first_name'] = $sKey;
            }

            if ($sVal === 'lastname' || $sVal === 'surname' || $sVal === 'familyname') {
                $this->aTmpData['last_name'] = $sKey;
            }

            if ($sVal === 'matchsex' || $sVal === 'looking' || $sVal === 'lookingfor') {
                $this->aTmpData['match_sex'] = $sKey;
            }

            if ($sVal === 'sex' || $sVal === 'gender') {
                $this->aTmpData['sex'] = $sKey;
            }

            if ($sVal === 'email' || $sVal === 'mail') {
                $this->aTmpData['email'] = $sKey;
            }

            if ($sVal === 'desc' || $sVal === 'description' || $sVal === 'descriptionme' ||
                $sVal === 'generaldescription' || $sVal === 'about' || $sVal === 'aboutme' ||
                $sVal === 'bio' || $sVal === 'biography' || $sVal === 'comment') {
                $this->aTmpData['description'] = $sKey;
            }

            if ($sVal === 'country' || $sVal === 'countryid') {
                $this->aTmpData['country'] = $sKey;
            }

            if ($sVal === 'city' || $sVal === 'town') {
                $this->aTmpData['city'] = $sKey;
            }

            if ($sVal === 'state' || $sVal === 'district' || $sVal === 'province' || $sVal === 'region') {
                $this->aTmpData['state'] = $sKey;
            }

            if ($sVal === 'zip' || $sVal === 'zipcode' || $sVal === 'postal' ||
                $sVal === 'postalcode' || $sVal === 'pin' || $sVal === 'pincode' || $sVal === 'eircode') {
                $this->aTmpData['zip_code'] = $sKey;
            }

            if ($sVal === 'website' || $sVal === 'site' || $sVal === 'url') {
                $this->aTmpData['website'] = $sKey;
            }

            if ($sVal === 'birthday' || $sVal === 'birthdate' || $sVal === 'dateofbirth' || $sVal === 'dob') {
                $this->aTmpData['birth_date'] = $sKey;
            }
        }
    }

    /**
     * Returns the error message for the form.
     *
     * @param int $iErrType
     *
     * @return string The error message.
     */
    private function getErrMsg($iErrType)
    {
        switch ($iErrType) {
            case static::ERR_BAD_FILE:
                $sErrMsg = t('Invalid File Format! Please select a valid CSV file containing the member data.');
                break;

            case static::ERR_TOO_LARGE:
                $sErrMsg = t('The file is too large. Please select a smaller file or change your server PHP settings. Especially "upload_max_filesize" and "post_max_size" directives in the php.ini file.');
                break;

            case static::ERR_INVALID:
                $sErrMsg = t('The file is Invalid/Empty or has incorrect Delimiter/Enclosure set.');
                break;
        }

        return $sErrMsg;
    }

    /**
     * Check (and modify if incorrect) the gender type.
     *
     * @param string $sSex
     *
     * @return string
     */
    private function checkGender($sSex)
    {
        $sSex = strtolower($sSex);

        if (!in_array($sSex, self::$aGenderList, true)) {
            $sSex = self::$aGenderList[mt_rand(0, 2)];
        }

        return $sSex;
    }

    /**
     * Remove the temporary file.
     *
     * @return void
     */
    private function removeTmpFile()
    {
        $this->file->deleteFile($this->aFile['tmp_name']);
    }

    /**
     * @return array
     */
    private function run()
    {
        $iErrType = $this->hasError();

        if ($iErrType !== static::NO_ERROR) {
            $this->removeTmpFile();
            $this->aRes = ['status' => false, 'msg' => $this->getErrMsg($iErrType)];
        } else {
            $this->setDefVals();
            $this->setTmpData();

            $iRow = 0;
            $oUserModel = new UserCoreModel;
            $oExistsModel = new ExistsCoreModel;
            $oValidate = new Validate;

            while ($this->aFileData !== false) {
                $sEmail = trim($this->aFileData[$this->aTmpData['email']]);
                if ($oValidate->email($sEmail) && !$oExistsModel->email($sEmail)) {
                    $this->setData($iRow);
                    $oUserModel->add(escape($this->aData[$iRow], true));
                    $iRow++;
                }
            }

            $this->removeTmpFile();
            fclose($this->rHandler);
            unset($this->rHandler, $oUserModel, $oExistsModel, $oValidate, $this->aTmpData, $this->aFileData, $this->aData);

            return [
                'status' => true,
                'msg' => nt('%n% user has been successfully added.', '%n% users has been successfully added.', $iRow)
            ];
        }
    }

    /**
     * @return int
     */
    private function hasError()
    {
        $sExtFile = $this->file->getFileExt($this->aFile['name']);

        if ($sExtFile !== self::IMPORT_FILE_EXTENSION) {
            return static::ERR_BAD_FILE;
        }

        if ($this->aFile['error'] === UPLOAD_ERR_INI_SIZE) {
            return static::ERR_TOO_LARGE;
        }

        if (!$this->rHandler || !$this->aFileData || !is_array($this->aFileData)) {
            return static::ERR_INVALID;
        }

        return static::NO_ERROR;
    }

    /**
     * Clean the text to make comparisons easier...
     *
     * @param $sValue
     *
     * @return string
     */
    private function cleanValue($sValue)
    {
        return strtolower(trim(str_replace(['-', '_', ' '], '', $sValue)));
    }
}
