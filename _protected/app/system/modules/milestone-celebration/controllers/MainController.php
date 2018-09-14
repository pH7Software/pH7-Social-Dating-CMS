<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2017-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Related Profile / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Meta;
use stdClass;

class MainController extends Controller
{
    public function awesome($iProfileId = null)
    {
        $this->view->page_title = $this->view->h1_title = t('You are AWESOME!!! 🎉');
        $this->notifyAdmin();
        $this->output();
    }

    private function notifyAdmin()
    {
        $oMilestoneNotifier = new MilestoneNotifier(new UserCoreModel, new Mail, $this->view);
    }
}
