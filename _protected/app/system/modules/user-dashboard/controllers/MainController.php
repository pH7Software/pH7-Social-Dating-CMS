<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User Dashboard / Controller
 */
namespace PH7;

class MainController extends Controller
{
    public function index()
    {
        $this->view->page_title = $this->view->h2_title = t('Your User Area');
        $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL .
            PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'style.css');

        $this->output();
    }
}
