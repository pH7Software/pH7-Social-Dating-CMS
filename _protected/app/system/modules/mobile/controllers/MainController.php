<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Mobile / Controller
 */
namespace PH7;

class MainController extends Controller
{

    private $sTitle;

    public function index()
    {
        $this->sTitle = t('Free Online Dating on Mobile Phone');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->meta_keywords = t('dating, free dating, dating site, mobile phone, mobile phone dating');
        $this->output();
    }

}
