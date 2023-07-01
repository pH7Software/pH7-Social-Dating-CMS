<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2023, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Xml / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Datatype\Type;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Xml\Exception as XmlException;
use PH7\Framework\Xml\Link;

class SitemapController extends MainController implements XmlControllable
{
    private const SITEMAP_TYPE = 'sitemap';

    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $this->sTitle = t('Site Map');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('Sitemap - Social Dating Service. Meet Single People with %site_name%');
        $this->view->h1_title = $this->sTitle;

        /*** Get the links ***/
        $sUrl = Uri::get('xml', 'sitemap', 'xmllink');

        try {
            $this->view->urls = (new Link($sUrl))->get();
        } catch (XmlException $oExcept) {
            $this->view->error = $oExcept->getMessage();
        }

        $this->output();
    }

    public function xmlLink(): void
    {
        parent::xmlLink();

        $this->view->display('links.xml.tpl');
    }

    public function xmlRouter(): void
    {
        $sAction = $this->httpRequest->get('action', Type::STRING);
        $this->generateXmlRouter($sAction);
        $this->sXmlType = self::SITEMAP_TYPE;
        $this->view->current_date = DateFormat::getSitemap(); // Date format for sitemap

        // XML router
        if (!empty($sAction)) {
            $this->generateXmlCommentRouter($sAction);
        } else {
            $this->sAction = 'home';
        }

        $this->xmlOutput();
    }

    private function generateXmlCommentRouter(string $sAction): void
    {
        switch ($sAction) {
            case 'main':
            case 'user':
            case 'blog':
            case 'note':
            case 'forums':
            case 'forum':
            case 'forum-topic':
            case 'comment':
            case 'picture':
            case 'video':
                $this->sAction = $sAction;
                break;

            case 'comment-profile':
            case 'comment-blog':
            case 'comment-note':
            case 'comment-picture':
            case 'comment-video':
                // Disable the cache since they are dynamic pages managed by the router
                $this->view->setCaching(false);
                $this->sAction = 'comment.inc';
                break;

            default:
                $this->displayPageNotFound(t('Sitemap Not Found!'));
        }
    }
}
