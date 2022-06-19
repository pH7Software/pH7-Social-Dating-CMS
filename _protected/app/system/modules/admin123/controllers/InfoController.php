<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Version;
use PH7\Framework\Url\Header;

class InfoController extends Controller
{
    private string $sTitle;

    public function index(): void
    {
        Header::redirect(
            Uri::get(
                PH7_ADMIN_MOD,
                'info',
                'software'
            )
        );
    }

    public function language(): void
    {
        $this->sTitle = t('PHP Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function software(): void
    {
        $this->sTitle = t('%software_name% Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->release_date = $this->dateTime->get(Version::KERNEL_RELEASE_DATE)->date();
        $this->view->license_form_link = Uri::get(PH7_ADMIN_MOD, 'setting', 'license');
        $this->view->tweet_msg_url = TweetSharing::getMessage();

        $this->output();
    }
}
