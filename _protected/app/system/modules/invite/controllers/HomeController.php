<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2013-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Invite / Controller
 */

declare(strict_types=1);

namespace PH7;

class HomeController extends Controller
{
    public function invitation(): void
    {
        $this->view->page_title = t('Invite your Friends');
        $this->view->meta_description = t('Invite your friends to join %site_name%');

        $this->output();
    }
}
