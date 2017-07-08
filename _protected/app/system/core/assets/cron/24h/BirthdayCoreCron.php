<?php
/**
 * @title            Birthday Cron Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2013-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / System / Core / Asset / Cron / 24H
 * @version          1.0
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

class BirthdayCoreCron extends Cron
{
    public function __construct()
    {
        parent::__construct();

        $this->send();
    }

    protected function send()
    {
        $iNum = (new BirthdayCore)->sendMails();

        if ($iNum == 0) {
            echo t('No birthday today.');
        } else {
            echo nt('%n% email sent.', '%n% emails sent.', $iNum);
        }
    }
}

// Go!
new BirthdayCoreCron;
