<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

class SearchController extends Controller
{

    public function index()
    {
        Framework\Url\Header::redirect(Framework\Mvc\Router\Uri::get('user', 'search', 'quick'));
    }

    public function quick()
    {
        $this->view->page_title = t('Search Quick Profiles');
        $this->view->h1_title = t('The Search Members | Quick Search');
        $this->output();
    }

    public function advanced()
    {
        $this->view->page_title = t('Search Advanced Profiles');
        $this->view->h1_title = t('The Search Members | Advanced Search');
        $this->output();
    }

}
