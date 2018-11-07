<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SearchController extends Controller
{
    public function buyer()
    {
        $this->view->page_title = $this->view->h1_title = t('Search Buyers');
        $this->output();
    }

    public function seller()
    {
        $this->view->page_title = $this->view->h1_title = t('Search Sellers');
        $this->output();
    }
}
