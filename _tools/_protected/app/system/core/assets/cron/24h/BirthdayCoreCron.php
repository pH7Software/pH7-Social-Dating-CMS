<?php
/**
 * @title            Birthday Cron Class
 *
 * @author           Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright        (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 24H
 * @version          1.0
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class BirthdayCoreCron extends Cron
{
    /** @var int */
    private $iNum;

    public function __construct()
    {
        parent::__construct();

        $this->send();
    }

    private function send()
    {
        $this->iNum = (new BirthdayCore)->sendMails();

        if ($this->hasBirthdays()) {
            echo t('No birthday today.');
        } else {
            echo nt('%n% email sent.', '%n% emails sent.', $this->iNum);
        }
    }

    /**
     * @return bool
     */
    private function hasBirthdays()
    {
        return $this->iNum === 0;
    }
}

// Go!
new BirthdayCoreCron;
