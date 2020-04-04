<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SearchController extends Controller
{
    public function index()
    {
        Header::redirect(
            Uri::get(
                'user',
                'search',
                'quick'
            )
        );
    }

    public function quick()
    {
        $this->view->page_title = $this->view->h1_title = t('Quick Search');
        $this->output();
    }

    public function advanced()
    {
        $this->view->page_title = $this->view->h1_title = t('Advanced Search');
        $this->output();
    }
}
