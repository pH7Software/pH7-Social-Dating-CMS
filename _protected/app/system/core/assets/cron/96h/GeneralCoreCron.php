<?php
/**
 * @title            General Cron Class
 * @desc             General Periodic Cron.
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
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

        $this->chmod();

        echo '<br />' . t('Done!') . '<br />';
        echo t('The Jobs Cron is working to complete successfully!');
    }

    /**
     * Checks file permissions and tries to correct them if they are incorrect.
     *
     * @return void
     */
    protected function chmod()
    {
        /** Check and correct the file permissions if necessary **/
        $this->file->chmod(PH7_PATH_ROOT . '_constants.php', 0644);
        $this->file->chmod(PH7_PATH_APP_CONFIG . 'config.ini', 0644);

        echo t('Chmod file... Ok!') . '<br />';
    }
}

// Go!
new GeneralCoreCron;
