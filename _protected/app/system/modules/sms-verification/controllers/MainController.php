<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Controller
 */

namespace PH7;

class MainController extends Controller
{
    public function send()
    {
        $this->view->page_title = $this->view->h1_title = t('Verify your phone number');

        $this->output();
    }

    public function verification()
    {
        $this->view->page_title = $this->view->h1_title = t('Phone number verification');

        $this->output();
    }
}
