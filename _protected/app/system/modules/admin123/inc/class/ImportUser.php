<?php

/**
 * @title          Import Users; Process Class
 *
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
        'ip',
        'is_active'
    ];

    /** @var resource|false */
    private $rHandler;

    private array $aFile;
    private array $aData = [];
    private array $aTmpData = [];

    /** @var array<string,int> Position of each recognised header in the CSV rows. */
    private array $aColumns = [];
    private array $aFileData;
    private array $aRes;
    private bool $bCanonicalMigration = false;
    private bool $bPreview;
    private bool $bRemoveTmpFile = false;
    private MemberMigrationCsv $oCsv;

    /**
     * @param array  $aFile      e.g., $_FILES['csv_file']
     * @param string $sDelimiter field delimiter (one character)
     * @param string $sEnclosure field enclosure (one character)
     */
    public function __construct(
        array $aFile,
        string $sDelimiter,
        string $sEnclosure,
        bool $bPreview = false
    ) {
        parent::__construct();

        $this->aFile = $aFile;
        $this->bPreview = $bPreview;
        $this->oCsv = new MemberMigrationCsv();

        try {
            $this->oCsv->assertDialect($sDelimiter, $sEnclosure);
        } catch (\InvalidArgumentException) {
            $this->aRes = ['status' => false, 'msg' => $this->getErrMsg(self::ERR_INVALID)];

            return;
        }

        $iErrType = $this->getUploadError();
        if ($iErrType !== self::NO_ERROR) {
            $this->aRes = ['status' => false, 'msg' => $this->getErrMsg($iErrType)];

            return;
        }

        $this->bRemoveTmpFile = is_uploaded_file((string)($this->aFile['tmp_name'] ?? ''));
        $this->rHandler = @fopen($this->aFile['tmp_name'], 'rb');
        if ($this->rHandler === false) {
            $this->aRes = ['status' => false, 'msg' => $this->getErrMsg(self::ERR_INVALID)];

            return;
        }

        $aHeader = fgetcsv($this->rHandler, 0, $sDelimiter, $sEnclosure, '\\');
        if (!is_array($aHeader) || $aHeader === []) {
            $this->closeHandler();
            $this->removeTmpFile();
            $this->aRes = ['status' => false, 'msg' => $this->getErrMsg(self::ERR_INVALID)];

            return;
        }

        $this->aFileData = $aHeader;
        $this->aRes = $this->run($sDelimiter, $sEnclosure);
    }

    /**
     * @return array (bool | string) ['status', 'msg']
     */
    public function getResponse(): array
    {
        return $this->aRes;
    }

    /**
     * Check and set the data from the CSV file.
     *
     * @param int   $iRow      Number of row of the CSV file
     * @param array $aUserData the values of that row
     */
    private function setData(int $iRow, array $aUserData): void
    {
        $oUser = new UserCore();

        foreach (self::DB_TYPES as $sType) {
            $sData = $this->getRowValue($aUserData, $sType);

            if ($sType === 'username') {
                $this->aData[$iRow][$sType] = $oUser->findUsername($sData, $this->aData[$iRow]['first_name'], $this->aData[$iRow]['last_name']);
            } elseif ($sType === 'password') {
                $this->aData[$iRow][$sType] = $sData !== '' ? $sData : bin2hex(random_bytes(32));
            } elseif ($sType === 'sex') {
                $this->aData[$iRow][$sType] = $this->fixGender($sData);
            } elseif ($sType === 'match_sex') {
                $this->aData[$iRow][$sType] = [
                    $sData !== '' ? $this->fixGender($sData) : $oUser->getMatchSex($this->aData[$iRow]['sex'])
                ];
            } elseif ($sType === 'birth_date') {
                $this->aData[$iRow][$sType] = $this->normalizeBirthDate($sData);
            } elseif ($sType === 'is_active') {
                $this->aData[$iRow][$sType] = $this->normalizeActivation($sData);
            } else {
                $this->aData[$iRow][$sType] = $sData;
            }
        }

        unset($oUser);
    }

    /**
     * The row's value when the CSV has a non-empty column for the field, otherwise its default.
     */
    private function getRowValue(array $aUserData, string $sType): string
    {
        return $this->oCsv->getValue(
            $aUserData,
            $this->aColumns,
            $this->aTmpData,
            $sType,
            $this->bCanonicalMigration
        );
    }

    /**
     * Set default values for the "ImportUser::$aTmpData" array.
     */
    private function setDefVals(): void
    {
        $sFiveChars = Various::genRnd($this->aFile['name'], 5);

        $this->aTmpData = [
            'email' => '',
            'username' => 'pH7Builder' . $sFiveChars,
            'password' => '',
            'first_name' => 'Alex' . $sFiveChars,
            'last_name' => 'Member' . $sFiveChars,
            'sex' => GenderTypeUserCore::FEMALE,
            'match_sex' => '',
            'birth_date' => $this->getRandomDate(),
            'country' => 'US',
            'city' => 'Virginia',
            'state' => 'Doswell',
            'zip_code' => '23047',
            'description' => '',
            'ip' => Ip::get(),
            'is_active' => (string)RegistrationCore::NO_ACTIVATION
        ];
    }

    private function setTmpData(): void
    {
        $this->aColumns = $this->oCsv->mapHeaders($this->aFileData);
        $this->bCanonicalMigration = $this->oCsv->isCanonicalMigration($this->aColumns);
    }

    /**
     * Returns the error message for the form.
     *
     * @return string the error message
     */
    private function getErrMsg(int $iErrType): string
    {
        return match ($iErrType) {
            self::ERR_BAD_FILE => t('Invalid file format. Select a CSV file containing member data.'),
            self::ERR_TOO_LARGE => t('The file is too large. Select a smaller file or increase the upload_max_filesize and post_max_size PHP settings.'),
            default => t('The CSV is empty, unreadable, or does not match the selected delimiter and enclosure.')
        };
    }

    /**
     * Check (and modify if incorrect) the gender type.
     */
    private function fixGender(string $sSex): string
    {
        $sSex = strtolower(trim($sSex));
        $aAliases = [
            'f' => GenderTypeUserCore::FEMALE,
            'woman' => GenderTypeUserCore::FEMALE,
            '2' => GenderTypeUserCore::FEMALE,
            'm' => GenderTypeUserCore::MALE,
            'man' => GenderTypeUserCore::MALE,
            '1' => GenderTypeUserCore::MALE,
            'c' => GenderTypeUserCore::COUPLE
        ];
        $sSex = $aAliases[$sSex] ?? $sSex;

        return GenderTypeUserCore::isGenderValid($sSex) ? $sSex : GenderTypeUserCore::FEMALE;
    }

    /**
     * Remove the temporary file.
     */
    private function removeTmpFile(): void
    {
        if ($this->bRemoveTmpFile && is_file((string)$this->aFile['tmp_name'])) {
            $this->file->deleteFile($this->aFile['tmp_name']);
        }
    }

    private function run(string $sDelimiter, string $sEnclosure): array
    {
        @set_time_limit(0);
        $this->setDefVals();
        $this->setTmpData();

        if (!isset($this->aColumns['email'])) {
            $this->closeHandler();
            $this->removeTmpFile();

            return ['status' => false, 'msg' => t('The CSV must contain an email column.')];
        }

        $aReport = [
            'importable' => 0,
            'imported' => 0,
            'invalid' => 0,
            'duplicate' => 0,
            'failed' => 0
        ];
        $aSeenEmails = [];
        $iRow = 1;
        $oUserModel = new UserCoreModel();
        $oExistsModel = new ExistCoreModel();
        $oValidate = new Validate();

        try {
            while (($aUserData = fgetcsv($this->rHandler, 0, $sDelimiter, $sEnclosure, '\\')) !== false) {
                ++$iRow;
                if ($this->isBlankRow($aUserData)) {
                    continue;
                }

                $sEmail = strtolower($this->getRowValue($aUserData, 'email'));
                if (!$oValidate->email($sEmail)) {
                    ++$aReport['invalid'];
                    continue;
                }

                if (isset($aSeenEmails[$sEmail]) || $oExistsModel->email($sEmail)) {
                    ++$aReport['duplicate'];
                    continue;
                }
                $aSeenEmails[$sEmail] = true;

                if ($this->bPreview) {
                    ++$aReport['importable'];
                    continue;
                }

                try {
                    $this->setData(0, $aUserData);
                    $aMemberData = escape($this->aData[0], true);
                    if ($this->aData[0]['birth_date'] === null) {
                        $aMemberData['birth_date'] = null;
                    }
                    $oUserModel->add($aMemberData);
                    unset($this->aData[0]);
                    ++$aReport['imported'];
                } catch (\Throwable $oException) {
                    unset($this->aData[0]);
                    ++$aReport['failed'];
                    error_log(sprintf(
                        'Member migration row %d failed: %s',
                        $iRow,
                        $oException->getMessage()
                    ));
                }
            }
        } finally {
            $this->closeHandler();
            $this->removeTmpFile();
        }

        return [
            'status' => true,
            'msg' => $this->buildResultMessage($aReport),
            'report' => $aReport
        ];
    }

    private function buildResultMessage(array $aReport): string
    {
        if ($this->bPreview) {
            return t(
                'Preview complete: %0% members can be imported; %1% rows are invalid and %2% already exist.',
                $aReport['importable'],
                $aReport['invalid'],
                $aReport['duplicate']
            );
        }

        return t(
            'Migration complete: %0% members imported; %1% rows invalid, %2% already existed and %3% failed.',
            $aReport['imported'],
            $aReport['invalid'],
            $aReport['duplicate'],
            $aReport['failed']
        );
    }

    private function isBlankRow(array $aRow): bool
    {
        return count(array_filter(
            $aRow,
            static fn ($mValue): bool => trim((string)$mValue) !== ''
        )) === 0;
    }

    /**
     * Generates a random (birth) date.
     */
    private function getRandomDate(): string
    {
        return date('Y') - mt_rand(20, 50) . '-' . mt_rand(1, 12) . '-' . mt_rand(1, 28);
    }

    private function normalizeBirthDate(string $sBirthDate): ?string
    {
        $oBirthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $sBirthDate);

        if ($oBirthDate instanceof \DateTimeImmutable && $oBirthDate->format('Y-m-d') === $sBirthDate) {
            return $sBirthDate;
        }

        return $this->bCanonicalMigration ? null : $this->aTmpData['birth_date'];
    }

    private function normalizeActivation(string $sActivation): int
    {
        $iActivation = (int)$sActivation;

        return in_array(
            $iActivation,
            [
                RegistrationCore::NO_ACTIVATION,
                RegistrationCore::EMAIL_ACTIVATION,
                RegistrationCore::MANUAL_ACTIVATION,
                RegistrationCore::SMS_ACTIVATION
            ],
            true
        ) ? $iActivation : RegistrationCore::MANUAL_ACTIVATION;
    }

    private function getUploadError(): int
    {
        $iUploadError = (int)($this->aFile['error'] ?? UPLOAD_ERR_NO_FILE);
        if (in_array($iUploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
            return self::ERR_TOO_LARGE;
        }

        if ($iUploadError !== UPLOAD_ERR_OK) {
            return self::ERR_INVALID;
        }

        $sFileName = (string)($this->aFile['name'] ?? '');
        if (strtolower($this->file->getFileExt($sFileName)) !== self::IMPORT_FILE_EXTENSION) {
            return self::ERR_BAD_FILE;
        }

        $sTmpPath = (string)($this->aFile['tmp_name'] ?? '');

        return $sTmpPath !== '' && is_file($sTmpPath) ? self::NO_ERROR : self::ERR_INVALID;
    }

    private function closeHandler(): void
    {
        if (is_resource($this->rHandler)) {
            fclose($this->rHandler);
        }

        $this->rHandler = false;
    }
}
