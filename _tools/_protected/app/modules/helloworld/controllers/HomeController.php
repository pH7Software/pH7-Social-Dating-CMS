<?php
/**
 * This module is just an example to show how easy you can create modules with pH7CMS
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Module / Hello World / Controller
 */

namespace PH7;

use PH7\Framework\Translate\Lang;

class HomeController extends Controller
{
    /**
     * Example URL: http://your-domain.com/m/helloworld/home/index/Pierre-Henry/Soria
     *
     * @param string $sFirstName
     * @param string $sLastName
     */
    public function index($sFirstName = '', $sLastName = '')
    {
        // Loading hello_world language...
        (new Lang)->load('hello_world');

        // Meta Tags
        $this->view->page_title = t('Hello World');
        $this->view->meta_description = t('This module is just an example to show how easy you can create modules with pH7CMS');
        $this->view->meta_keywords = t('hello world, test, developpers, CMS, Dating CMS, CMS Dating, Social CMS, pH7, pH7 CMS, Dating Script, Social Dating Script, Dating Software, Social Network Software, Social Networking Software');

        /* Heading html tags (H1 to H4) */
        $this->view->h1_title = t('Example of a simple module that displays "Hello World"');
        $this->view->h3_title = t('H3 title example');
        $this->view->desc = t('Hello %0% %1% How are you on this %2%?', $this->str->upperFirst($sFirstName), $this->str->upperFirst($sLastName), $this->dateTime->get()->date('l'));

        // Display the page
        $this->output();
    }
}
