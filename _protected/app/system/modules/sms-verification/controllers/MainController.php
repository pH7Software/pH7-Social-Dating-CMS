<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
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
