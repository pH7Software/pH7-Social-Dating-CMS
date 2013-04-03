<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 * @version          1.0
 */

namespace PH7;

class Cron extends Framework\Cron\Run\Cron
{

    /**
     * Check if the cron has already been executed.
     *
     * @return void If cron has already been executed, the script stops with exit() function and an explanatory message.
     */
    public function isAlreadyExec()
    {
        if (!$this->checkDelay())
            exit(t('This cron has already been executed.'));
    }

}
