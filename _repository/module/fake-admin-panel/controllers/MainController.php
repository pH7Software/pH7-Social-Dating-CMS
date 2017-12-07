<?php
/**
 * @title          Main Controller
 * @desc           Reproduces a false administration interface identical to the real interface.
 *
 * @author         Pierre-Henry Soria <hi@ph7.me>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Meta;

class MainController extends Controller
{
    public function login()
    {
        $this->view->header = Meta::NOINDEX;
        $this->view->page_title = t('Sign in to Admin Panel');
        $this->view->h1_title = t('Admin Panel - Login');

        $this->output();
    }
}
