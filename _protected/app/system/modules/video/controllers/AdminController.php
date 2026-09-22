<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2016-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Connect / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminController extends MainController
{
    /**
     * The video admin area has a single page. Without this, the index() inherited from
     * MainController looks for a missing "admin/index.tpl" and fails with a server error.
     */
    public function index()
    {
        Header::redirect(
            Uri::get('video', 'admin', 'config')
        );
    }

    public function config()
    {
        $this->view->page_title = $this->view->h2_title = t('Youtube API Key - Setting');
        $this->output();
    }
}
