<?php
/**
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / App / Module / Hello World / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;
use PH7\Framework\Translate\Lang;

class SecureController extends Controller
{
    public function index()
    {
        // Loading Gettext Language File ...
        (new Lang)->load('hello_world');

        $sUsr = $this->config->values['module.setting']['user'];
        $sPwd = $this->config->values['module.setting']['password'];

        if (Http::requireAuth($sUsr, $sPwd)) {
            // Meta Tags
            $this->view->page_title = t('HTTP Secure Page');
            $this->view->meta_description = t('Simple HTTP Secure Page');
            $this->view->meta_keywords = t('secure,CMS,PHP,framework,MVC,page,HTTP');

            /* H TITLE html tag H1 to H4 */
            $this->view->h1_title = t('HTTP Secure Page');

            // Assign variables to the template
            $this->view->user = $sUsr;

            // Output Template
            $this->output();
        }
    }
}
