<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Contact / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Meta;

class ContactController extends Controller
{
    public function index(): void
    {
        /**
         * SEO - Google shouldn't waste time indexing a contact form.
         * Instead, it will use that time indexing more important pages of the website :)
         */
        $this->view->header = Meta::NOINDEX;

        $this->view->page_title = t('Contact Us');
        $this->view->h1_title = t('Contact %site_name%');

        $this->output();
    }
}
