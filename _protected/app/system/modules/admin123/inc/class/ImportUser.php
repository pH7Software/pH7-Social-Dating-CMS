<?php
/**
 * @title          Import Users; Process Class
 * @desc           Import new Users from CSV data file.
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2015-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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
    private const NO_ERROR = 0;
    private const ERR_BAD_FILE = 1;
    private const ERR_TOO_LARGE = 2;
    private const ERR_INVALID = 3;

    private const IMPORT_FILE_EXTENSION = 'csv';

    /*
     * @var array Contains the DB data types.
     */
    private const DB_TYPES = [
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
        'ip'
    ];

    /** @var resource|false */
    private $rHandler;

    private array $aFile;
    private array $aData = [];
    private array $aTmpData = [];
    private array $aFileData;
    private array $aRes;

    /**
     * @param array $aFile e.g., $_FILES['csv_file']
     * @param string $sDelimiter Field delimiter (one character).
     * @param string $sEnclosure Field enclosure (one character).
     */
    public function __construct(array $aFile, string $sDelimiter, string $sEnclosure)
    {
        parent::__construct();

        // Initialize the necessary attributes
        $this->aFile = $aFile;
        $this->rHandler = @fopen($this->aFile['tmp_name'], 'rb');
        $this->aFileData = (array)@fgetcsv($this->rHandler, 0, $sDelimiter, $sEnclosure);
        $this->aRes = $this->run($sDelimiter, $sEnclosure);
    }

    /**
     * @return array (boolean | string) ['status', 'msg']
     */
    public function getResponse(): array
    {
        return $this->aRes;
    }

    /**
     * Check and set the data from the CSV file.
     *
     * @param int $iRow Number of row of the CSV file
     */
    private function setData(int $iRow): void
    {
        $oUser = new UserCore;

        foreach (self::DB_TYPES as $sType) {
            $sData = !empty($this->aFileData[$this->aTmpData[$sType]]) ? trim($this->aFileData[$this->aTmpData[$sType]]) : $this->aTmpData[$sType];

            if ($sType === 'username') {
                $this->aData[$iRow][$sType] = $oUser->findUsername($sData, $this->aData[$iRow]['first_name'], $this->aData[$iRow]['last_name']);
            } elseif ($sType === 'sex') {
                $this->aData[$iRow][$sType] = $this->fixGender($sData);
            } elseif ($sType === 'match_sex') {
                $this->aData[$iRow][$sType] = [$this->fixGender($sData)];
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
     */
    private function setDefVals(): void
    {
        $sFiveChars = Various::genRnd($this->aFile['name'], 5);

        $this->aTmpData = [
            'email' => $this->getRandomEmail($sFiveChars),
            'username' => 'pH7Builder' . $sFiveChars,
            'password' => Various::genRnd(),
            'first_name' => 'Alex' . $sFiveChars,
            'last_name' => 'Rolli' . $sFiveChars,
            'sex' => GenderTypeUserCore::GENDERS[array_rand(GenderTypeUserCore::GENDERS)], // Generate gender randomly
            'match_sex' => GenderTypeUserCore::GENDERS[array_rand(GenderTypeUserCore::GENDERS)], // Generate one randomly
            'birth_date' => $this->getRandomDate(),
            'country' => 'US',
            'city' => 'Virginia',
            'state' => 'Doswell',
            'zip_code' => '23047',
            'description' => 'Hi all!<br />How are you today?<br /> Bye ;)',
            'ip' => Ip::get()
        ];
    }

    private function setTmpData(): void
    {
        foreach ($this->aFileData as $sKey => $sVal) {
            $sVal = $this->cleanValue($sVal);

            // Test comparisons of strings and adding values in an array "ImportUser::$aTmpData"
            if ($sVal === 'username' || $sVal === 'login' || $sVal === 'user' || $sVal === 'nickname') {
                $this->aTmpData['username'] = $sKey;
            }

            if ($sVal === 'name' || $sVal === 'firstname' || $sVal === 'givenname' || $sVal === 'forename') {
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

            if ($sVal === 'email' || $sVal === 'mail' || $sVal === 'emailid') {
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

            if (
                $sVal === 'zip' || $sVal === 'zipcode' || $sVal === 'postal' || $sVal === 'postcode' ||
                $sVal === 'postalcode' || $sVal === 'pin' || $sVal === 'pincode' || $sVal === 'eircode'
            ) {
                $this->aTmpData['zip_code'] = $sKey;
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
    private function getErrMsg(int $iErrType): string
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
     */
    private function fixGender(string $sSex): string
    {
        $sSex = strtolower($sSex);

        if (!GenderTypeUserCore::isGenderValid($sSex)) {
            $sSex = GenderTypeUserCore::GENDERS[array_rand(GenderTypeUserCore::GENDERS)];
        }

        return $sSex;
    }

    /**
     * Remove the temporary file.
     */
    private function removeTmpFile(): void
    {
        $this->file->deleteFile($this->aFile['tmp_name']);
    }

    private function run(string $sDelimiter, string $sEnclosure): array
    {
        $iErrType = $this->hasError();

        if ($iErrType !== static::NO_ERROR) {
            $this->removeTmpFile();
            return ['status' => false, 'msg' => $this->getErrMsg($iErrType)];
        } else {
            $this->setDefVals();
            $this->setTmpData();

            $iRow = 0;
            $oUserModel = new UserCoreModel;
            $oExistsModel = new ExistCoreModel;
            $oValidate = new Validate;

            while (false !== ($aUserData = fgetcsv($this->rHandler, 0, $sDelimiter, $sEnclosure))) {
                $sEmail = trim($aUserData[$this->aTmpData['email']]);

                // Make sure the email is valid and doesn't exist yet in the database
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
     * Generates a random (birth) date.
     */
    private function getRandomDate(): string
    {
        return date('Y') - mt_rand(20, 50) . '-' . mt_rand(1, 12) . '-' . mt_rand(1, 28);
    }

    private function hasError(): int
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

    private function getRandomEmail(string $sFiveChars): string
    {
        return sprintf('peterzhenry%s@%s.ph7builder.com', $sFiveChars, $sFiveChars);
    }

    /**
     * Clean the text to make comparisons easier...
     */
    private function cleanValue(string $sValue): string
    {
        return strtolower(
            trim(
                str_replace(
                    ['-', '_', ' '],
                    '',
                    $sValue
                )
            )
        );
    }
}
