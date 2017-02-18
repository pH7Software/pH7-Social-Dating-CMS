<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

abstract class Cron extends Framework\Cron\Run\Cron
{
    public function __construct()
    {
        parent::__construct();

        // Check delay
        $this->isAlreadyExec();
    }

    /**
     * Check if the cron has already been executed.
     *
     * @return void If cron has already been executed, the script stops with exit() function and an explanatory message.
     */
    public function isAlreadyExec()
    {
        if (!$this->checkDelay()) {
            Framework\Http\Http::setHeadersByCode(403);
            exit(t('This cron has already been executed.'));
        }
    }
}
