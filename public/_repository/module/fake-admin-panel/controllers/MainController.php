<?php
/**
 * @title          Main Controller
 * @desc           Reproduces a false administration interface identical to the real interface.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Controller
 * @version        1.1.0
 */

namespace PH7;

class MainController extends Controller
{

    public function login()
    {
        $this->view->header = '<meta name="robots" content="noindex" />';
        $this->view->page_title = t('Sign in to Admin Panel');
        $this->view->page_h1 = t('Sign in');
        $this->output();
    }

}
