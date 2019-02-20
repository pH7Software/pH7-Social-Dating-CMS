<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Include / Class
 */

namespace PH7;

use PH7\Framework\Http\Http;
use Teapot\StatusCode;

abstract class Cron extends Framework\Cron\Run\Cron
{
    public function __construct()
    {
        parent::__construct();

        $this->isAlreadyExecuted();
    }

    /**
     * Check the delay and see if the cron has already been executed.
     *
     * @return void If cron has already been executed, the script stops with exit() function and an explanatory message.
     *
     * @throws Framework\Http\Exception
     */
    private function isAlreadyExecuted()
    {
        if (!$this->checkDelay()) {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);

            exit(t('This cron has already been executed.'));
        }
    }
}
