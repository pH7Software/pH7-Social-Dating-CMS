<?php
/**
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / Include / Class
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Http\Http;
use PH7\JustHttp\StatusCode;

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
    private function isAlreadyExecuted(): void
    {
        if (!$this->checkDelay()) {
            Http::setHeadersByCode(StatusCode::FORBIDDEN);

            exit(t('This cron has already been executed.'));
        }
    }
}
