<?php
/**
 * @title            Cron Class
 * @desc             Generic class for the Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cron / Run
 */

namespace PH7\Framework\Cron\Run;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Url\Uri;

abstract class Cron extends \PH7\Framework\Core\Core
{

    protected $iTime;
    private $_oUri;

    public function __construct()
    {
        parent::__construct();

        $this->iTime = time();
        $this->_oUri = Uri::getInstance();
    }

    /**
     * @return boolean Returns TRUE if the delay is valid, otherwise FALSE.
     */
    public function checkDelay()
    {
        $sFullPath = PH7_PATH_SYS . 'core/assets/cron/_delay/' . $this->getFileName() . '.txt';
        $bStatus = true; // Default status is TRUE

        if ($this->file->existFile($sFullPath))
        {
            $iSavedTime = $this->file->getFile($sFullPath);
            $iSeconds = $this->getDelay() * 3600; // Convert hours to seconds
            $iCronTime = $iSavedTime + $iSeconds;

            $bStatus = ($iCronTime < $this->iTime); // Status is FALSE if the delay has not yet elapsed

            if ($bStatus)
                $this->file->deleteFile($sFullPath);
        }

        if ($bStatus)
            $this->file->putFile($sFullPath, $this->iTime);

        return $bStatus;
    }

    /**
     * Get file name.
     *
     * @return string File name.
     */
    protected function getFileName()
    {
        return strtolower($this->_oUri->fragment(3));
    }

    /**
     * Get the cron delay.
     *
     * @return integer Delay in hour.
     */
    protected function getDelay()
    {
        /**
         * @internal We cast the value into integer type to get only the integer data (without the 'h' character).
         */
        return (int) $this->_oUri->fragment(2);
    }

}
