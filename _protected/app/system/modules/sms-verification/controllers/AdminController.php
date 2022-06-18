<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / SMS Verification / Controller
 */

namespace PH7;

class AdminController extends MainController
{
    public function config()
    {
        $this->view->page_title = $this->view->h2_title = t('Configuration - SMS Gateways');
        $this->output();
    }
}
