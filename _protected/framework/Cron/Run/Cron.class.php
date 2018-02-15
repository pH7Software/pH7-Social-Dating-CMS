<?php
/**
 * @title            Cron Class
 * @desc             Generic class for the Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cron / Run
 */

namespace PH7\Framework\Cron\Run;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Core\Core;
use PH7\Framework\Url\Uri;

abstract class Cron extends Core
{
    const URI_INDEX_FILENAME = 3;
    const URI_INDEX_DELAY = 2;
    const HOUR_IN_SECS = 3600;
    const DELAY_FILE_EXT = '.txt';

    /** @var int */
    protected $iTime;

    /** @var Uri */
    private $oUri;

    public function __construct()
    {
        parent::__construct();

        $this->iTime = time();
        $this->oUri = Uri::getInstance();
    }

    /**
     * @return bool Returns TRUE if the delay is valid, otherwise FALSE.
     */
    public function checkDelay()
    {
        $sFullPath = PH7_PATH_SYS . 'core/assets/cron/_delay/' . $this->getFileName() . self::DELAY_FILE_EXT;
        $bStatus = true; // Default status is TRUE

        if ($this->file->existFile($sFullPath)) {
            $iSavedTime = $this->file->getFile($sFullPath);
            $iHours = $this->getDelay();
            $iCronTime = $iSavedTime + $this->convertHoursToSeconds($iHours);

            // Status is FALSE if the delay has not yet elapsed
            $bStatus = ($iCronTime < $this->iTime);

            if ($bStatus) {
                $this->file->deleteFile($sFullPath);
            }
        }

        if ($bStatus) {
            $this->file->putFile($sFullPath, $this->iTime);
        }

        return $bStatus;
    }

    /**
     * @return string File name.
     */
    protected function getFileName()
    {
        return strtolower($this->oUri->fragment(self::URI_INDEX_FILENAME));
    }

    /**
     * Get the cron delay.
     *
     * @return int Delay in hour.
     */
    protected function getDelay()
    {
        /**
         * @internal We cast the value into integer type to get only the integer data (without the 'h' character).
         */
        return (int)$this->oUri->fragment(self::URI_INDEX_DELAY);
    }

    /**
     * @param int $iHours Hour(s).
     *
     * @return int Seconds
     */
    private function convertHoursToSeconds($iHours)
    {
        return $iHours * self::HOUR_IN_SECS;
    }
}
