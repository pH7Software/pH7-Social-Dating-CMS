<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Invite / Controller
 */

namespace PH7;

class HomeController extends Controller
{
    public function invitation()
    {
        $this->view->page_title = t('Invite your Friends');
        $this->view->meta_description = t('Invite your friends to join %site_name%');

        $this->output();
    }
}
