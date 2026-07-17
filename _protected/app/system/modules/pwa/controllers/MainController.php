<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2018-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Router\Uri;

class MainController extends Controller
{
    public const MANIFEST_CONTENT_TYPE = 'application/manifest+json';
    public const JSON_TPL_EXT = '.json.tpl';
    public const XML_CONTENT_TYPE = 'application/xml';
    public const XML_TPL_EXT = '.xml.tpl';
    public const HTML_TPL_EXT = '.html.tpl';
    public const STATIC_CACHE_LIFETIME = 86400; // 86400 secs = 24 hours

    public function manifest()
    {
        $this->enableStaticTplCache();

        $this->view->hex_bg_color = $this->config->values['module.setting']['hex.background_color'];
        $this->view->orientation = $this->config->values['module.setting']['orientation_mode'];

        /* App shortcuts (long-press/right-click on the installed app icon) */
        $this->view->browse_members_url = Uri::get('user', 'browse', 'index');
        $this->view->inbox_url = Uri::get('mail', 'main', 'inbox');
        $this->view->my_account_url = Uri::get('user', 'account', 'index');

        $this->jsonOutput();
    }

    public function browserConfig()
    {
        $this->enableStaticTplCache();

        $this->view->hex_title_color = $this->config->values['module.setting']['hex.title_color'];

        $this->xmlOutput();
    }

    /**
     * Standalone offline fallback page, pre-cached by the service worker.
     * Self-contained on purpose (inline styles, no external assets) since it is served without network.
     */
    public function offline()
    {
        $this->enableStaticTplCache();

        /* Standalone document; don't wrap it in the site layout */
        $this->view->display($this->httpRequest->currentController() . PH7_DS . $this->registry->action . self::HTML_TPL_EXT);
    }

    /**
     * @throws Framework\Http\Exception
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     *
     * @return void
     */
    private function jsonOutput()
    {
        /* Compression damages JSON syntax, so disable them */
        $this->view->setHtmlCompress(false);
        $this->view->setPhpCompress(false);

        $this->setJsonContentType();

        $this->view->display($this->httpRequest->currentController() . PH7_DS . $this->registry->action . self::JSON_TPL_EXT);
    }

    /**
     * @throws Framework\Http\Exception
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     *
     * @return void
     */
    private function xmlOutput()
    {
        /* Don't Compress XML */
        $this->view->setHtmlCompress(false);
        $this->view->setPhpCompress(false);

        $this->setXmlContentType();

        $this->view->display($this->httpRequest->currentController() . PH7_DS . $this->registry->action . self::XML_TPL_EXT);
    }

    private function enableStaticTplCache()
    {
        $this->view->setCaching(true);
        $this->view->setCacheExpire(self::STATIC_CACHE_LIFETIME);
    }

    /**
     * Set the appropriate header output for JSON format.
     *
     * @throws Framework\Http\Exception
     *
     * @return void
     */
    private function setJsonContentType()
    {
        Http::setContentType(self::MANIFEST_CONTENT_TYPE);
    }

    /**
     * Set the appropriate header output for XML format.
     *
     * @throws Framework\Http\Exception
     *
     * @return void
     */
    private function setXmlContentType()
    {
        Http::setContentType(self::XML_CONTENT_TYPE);
    }
}
