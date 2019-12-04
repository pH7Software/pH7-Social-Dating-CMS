<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class / Design
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;

class UserDesignCore extends Design
{
    const NONE_IMG_FILENAME = 'none.svg';
    const PENDING_IMG_FILENAME = 'pending.svg';

    /**
     * Ajax counter endpoint to count the number of users registered on the site.
     *
     * @return void
     */
    public function userCounter()
    {
        $this->staticFiles('js', PH7_STATIC . PH7_JS, 'jquery/counter.js,Stat.js');
        echo '<div class="stat_total_users"></div>';
    }
}
