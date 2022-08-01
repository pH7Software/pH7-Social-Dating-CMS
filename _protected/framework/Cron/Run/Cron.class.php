<?php
/**
 * @desc             Generic class for the Periodic Cron.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / Framework / Cron / Run
 */

declare(strict_types=1);

namespace PH7\Framework\Cron\Run;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Core;
use PH7\Framework\Url\Uri;

abstract class Cron extends Core
{
    private const URI_FILENAME_INDEX = 3;
    private const URI_DELAY_INDEX = 2;
    private const HOUR_IN_SECS = 3600;
    private const DELAY_FILE_EXT = '.txt';

    /**
     * If the server takes time loading the script,
     * we add 10 secs to make sure it won't be seen as "already ran within the cron delay"
     */
    private const DELAY_TOLERANCE_INTERVAL_IN_SEC = 10;

    private int $iTime;

    private Uri $oUri;

    private string $sDelayPathFile;

    public function __construct()
    {
        parent::__construct();

        $this->iTime = time();
        $this->oUri = Uri::getInstance();
        $this->sDelayPathFile = $this->getDelayedFilePath();
    }

    /**
     * @return bool Returns TRUE if the delay is valid, otherwise FALSE.
     */
    protected function checkDelay(): bool
    {
        $bStatus = true; // Default delay status is TRUE

        if ($iSavedTime = $this->getSavedDelay()) {
            $iHours = $this->getCronDelay();
            $iCronTime = $iSavedTime + $this->convertHoursToSeconds($iHours);

            $bStatus = $this->hasDelayPassed($iCronTime);
            if ($bStatus) {
                $this->file->deleteFile($this->sDelayPathFile);
            }
        }

        if ($bStatus) {
            $this->file->putFile($this->sDelayPathFile, $this->iTime);
        }

        return $bStatus;
    }

    /**
     * @return int|false The file contents if it exists, FALSE otherwise.
     * TODO PHP v8.2 will support `false` as standalone type. Will be able to be stricter by replace return types with "int|false"
     */
    private function getSavedDelay(): int|bool
    {
        if ($this->file->existFile($this->sDelayPathFile)) {
            return (int)$this->file->getFile($this->sDelayPathFile);
        }

        return false;
    }

    /**
     * @return string The current cron filename.
     */
    private function getFileName(): string
    {
        return strtolower($this->oUri->fragment(self::URI_FILENAME_INDEX));
    }

    /**
     * @return string The full path of the delayed text file containing the UNIX time of the last cron job execution.
     */
    private function getDelayedFilePath(): string
    {
        return PH7_PATH_SYS . 'core/assets/cron/_delay/' . $this->getFileName() . self::DELAY_FILE_EXT;
    }

    /**
     * Get the cron delay.
     *
     * @return int Delay in hour.
     */
    private function getCronDelay(): int
    {
        /**
         * @internal We cast the value into integer type to get only the integer data (without the 'h' character).
         */
        return (int)$this->oUri->fragment(self::URI_DELAY_INDEX);
    }

    /**
     * @param int $iCronTime
     *
     * @return bool Returns FALSE if the delay hasn't yet elapsed, TRUE otherwise.
     */
    private function hasDelayPassed($iCronTime): bool
    {
        return $iCronTime <= $this->iTime + self::DELAY_TOLERANCE_INTERVAL_IN_SEC;
    }

    /**
     * @param int $iHours Hour(s).
     *
     * @return int Seconds
     */
    private function convertHoursToSeconds(int $iHours): int
    {
        return $iHours * self::HOUR_IN_SECS;
    }
}
