<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Contact / Controller
 */

namespace PH7;

class ContactController extends Controller
{
    public function index()
    {
        $this->view->page_title = t('Contact Us');
        $this->view->h1_title = t('Contact %site_name%');
        $this->output();
    }
}
