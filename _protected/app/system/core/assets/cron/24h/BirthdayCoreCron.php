<?php
/**
 * @title            Birthday Cron Class
 *
 * @author           Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright        (c) 2013-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license          MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 24H
 * @version          1.2
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

class BirthdayCoreCron extends Cron
{
    private int $iNum;

    public function __construct()
    {
        parent::__construct();

        $this->send();
    }

    private function send(): void
    {
        $this->iNum = (new BirthdayCore)->sendMails();

        if ($this->hasBirthdays()) {
            echo t('No birthday today.');
        } else {
            echo nt('%n% email sent.', '%n% emails sent.', $this->iNum);
        }
    }

    private function hasBirthdays(): bool
    {
        return $this->iNum === 0;
    }
}

// Go!
new BirthdayCoreCron;
