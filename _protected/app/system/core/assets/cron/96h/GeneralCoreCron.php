<?php
/**
 * @desc             General Periodic Cron.
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 96H
 */

declare(strict_types=1);

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
     */
    private function chmod(): void
    {
        /** Check and correct the file permissions if necessary **/
        $this->file->chmod(PH7_PATH_ROOT . '_constants.php', Chmod::MODE_WRITE_READ);
        $this->file->chmod(PH7_PATH_APP_CONFIG . 'config.ini', Chmod::MODE_WRITE_READ);

        echo t('Chmod file... Ok!') . '<br />';
    }
}

// Go!
new GeneralCoreCron;
