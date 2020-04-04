<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module /PWA / Controller
 */

namespace PH7;

use PH7\Framework\Http\Http;

class MainController extends Controller
{
    const CONTENT_TYPE = 'application/json';
    const JSON_TPL_EXT = '.json.tpl';
    const STATIC_CACHE_LIFETIME = 86400; // 86400 secs = 24 hours

    public function manifest()
    {
        $this->setContentType();
        $this->enableStaticTplCache();

        $this->view->bg_color = $this->config->values['module.setting']['background_color'];
        $this->view->orientation = $this->config->values['module.setting']['orientation_mode'];

        $this->jsonOutput();
    }

    /**
     * @return void
     *
     * @throws Framework\Http\Exception
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function jsonOutput()
    {
        /* Compression damages JSON syntax, so disable them */
        $this->view->setHtmlCompress(false);
        $this->view->setPhpCompress(false);

        $this->setContentType();

        $this->view->display($this->httpRequest->currentController() . PH7_DS . $this->registry->action . self::JSON_TPL_EXT);
    }

    private function enableStaticTplCache()
    {
        $this->view->setCaching(true);
        $this->view->setCacheExpire(self::STATIC_CACHE_LIFETIME);
    }

    /**
     * Set the appropriate header output format.
     *
     * @return void
     *
     * @throws Framework\Http\Exception
     */
    private function setContentType()
    {
        Http::setContentType(self::CONTENT_TYPE);
    }
}
