<?php
/**
 * @title            Cron Class
 * @desc             Generic class for the Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Cron / Run
 * @version          1.0
 */

namespace PH7\Framework\Cron\Run;
defined('PH7') or exit('Restricted access');

abstract class Cron extends \PH7\Framework\Core\Core
{

    protected $sCurrentDate;

    public function __construct()
    {
        parent::__construct();

        $this->sCurrentDate = $this->dateTime->get()->dateTime('Y-m-d H');
    }

    /**
     * @return boolean Returns TRUE if the delay is valid, otherwise FALSE.
     */
    public function checkDelay()
    {
        $sFullPath = PH7_PATH_SYS . 'core/assets/cron/_delay/' . $this->getFileName() . '.txt';

        if (!$this->file->existsFile($sFullPath))
        {
            $this->file->putFile($sFullPath, $this->sCurrentDate);
            return true;
        }

        $sData = $this->file->getFile($sFullPath);
        $sCronDate = $sData + $this->getDelay();

        $bRet = ($sCronDate < $this->sCurrentDate);

        if ($bRet)
            $this->file->deleteFile($sFullPath);

        return $bRet;
    }

    /**
     * Get file name.
     *
     * @return string File name.
     */
    protected function getFileName()
    {
        return str_replace(array('\\', 'ph7', 'core', 'cron'), '', strtolower(get_called_class()));
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
        return (int) \PH7\Framework\Url\Uri::getInstance()->fragment(2);
    }

}
