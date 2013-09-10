<?php
/**
 * @title          Admin Controller
 * @desc           Configuring the honeypot and viewing log.
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License <http://www.gnu.org/licenses/gpl.html>
 * @package        PH7 / App / Module / Fake Admin Panel / Controller
 * @version        1.1.0
 */

namespace PH7;

class AdminController extends MainController
{

    public function index()
    {
        $this->sTitle = t('Logs | Administration of Fake Admin');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->path_log =  '?dir=' . PH7_MOD . $this->registry->module . PH7_DS . PH7_INC . Logger::ATTACK_DIR;
        $this->output();
    }

    public function config()
    {
        $this->sTitle = t('Config | Fake Admin Honeypot');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

}
