<?php
/**
 * @title            General Cron Class
 * @desc             General Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 * @version          1.0
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

class GeneralCoreCron extends Cron
{

    public function __construct()
    {
        parent::__construct();

        $this->isAlreadyExec();

        $this->chmod();

        echo '<br />' . t('Done!') . '<br />';
        echo t('The Jobs Cron is working to complete successfully!');
    }


    /**
     * Checks file permissions and tries to correct them if they are incorrect.
     *
     * @access protected
     * @return void
     */
    protected function chmod()
    {
        $oFile = new Framework\File\File;

        /** Check and correct the file permissions if necessary **/
        $oFile->chmod(PH7_PATH_ROOT . '_constants.php', 0644);
        $oFile->chmod(PH7_PATH_TMP . \PH7\Framework\Core\License::FILE, 0444);
        $oFile->chmod(PH7_PATH_APP_CONFIG . 'config.ini', 0644);

        unset($oFile);

        echo t('Chmod file... Ok!') . '<br />';
    }

}

// Go!
new GeneralCoreCron;
