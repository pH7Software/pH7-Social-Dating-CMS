<?php
/**
 * @title            General Cron Class
 * @desc             General Periodic Cron.
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 * @version          1.0
 */

namespace PH7;

use PH7\Framework\File\Permission\Chmod;

defined('PH7') or exit('Restricted access');

class GeneralCoreCron extends Cron
{
    public function __construct()
    {
        parent::__construct();

        $this->chmod();

        echo '<br />' . t('Cron job finished!');
    }

    /**
     * Checks file permissions and tries to correct them if they are incorrect.
     *
     * @return void
     */
    private function chmod()
    {
        /** Check and correct the file permissions if necessary **/
        $this->file->chmod(PH7_PATH_ROOT . '_constants.php', Chmod::MODE_WRITE_READ);
        $this->file->chmod(PH7_PATH_APP_CONFIG . 'config.ini', Chmod::MODE_WRITE_READ);

        echo t('Chmod file... Ok!') . '<br />';
    }
}

// Go!
new GeneralCoreCron;
