<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Module / Hello World / Controller
 */
namespace PH7;

class HomeController extends Controller
{

    /**
     * Example URL: http://your-domain.com/m/helloworld/home/index/Pierre-Henry/Soria
     */
    public function index ($sFirstName = '', $sLastName = '')
    {
        // Loading hello_world language...
        $this->lang->load('hello_world');

        // Meta Tags
        $this->view->page_title = t('Hello World');
        $this->view->meta_description = t('This module is a test for create simple module');
        $this->view->meta_keywords = t('hello world, test, developpers, CMS, Dating CMS, CMS Dating, Social CMS, pH7, pH7 CMS, Dating Script, Social Dating Script, Dating Software, Social Network Software, Social Networking Software');

        /* H TITLE html tag H1 to H4 */
        $this->view->h1_title = t('Example of simple module that displays hello world for the CMS');
        $this->view->h3_title = t('H3 title example');
        $this->view->desc = t('Hello world %0% %1%, how are you on %2%?', $this->str->upperFirst($sFirstName), $this->str->upperFirst($sLastName), $this->dateTime->get()->date('Y-m-d'));

        // Go Display
        $this->output();
    }

}
