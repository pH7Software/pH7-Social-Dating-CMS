<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Xml / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Xml\Exception as XmlException;
use PH7\Framework\Xml\Link;

class SitemapController extends MainController implements XmlControllable
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->sTitle = t('Site Map');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('Map of Website, Free Online Social Dating Website with Video Chat Rooms, Meet Single People with %site_name%');
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

    public function xmlLink()
    {
        parent::xmlLink();

        $this->view->display('links.xml.tpl');
    }

    public function xmlRouter()
    {
        $sAction = $this->httpRequest->get('action');
        $this->generateXmlRouter($sAction);
        $this->sXmlType = 'sitemap';
        $this->view->current_date = DateFormat::getSitemap(); // Date format for sitemap

        // XML router
        if (!empty($sAction)) {
            $this->generateXmlCommentRouter($sAction);
        } else {
            $this->sAction = 'home';
        }

        $this->xmlOutput();
    }

    /**
     * @param string $sAction
     *
     * @return void
     */
    private function generateXmlCommentRouter($sAction)
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
            case 'game':
                $this->sAction = $sAction;
                break;

            case 'comment-profile':
            case 'comment-blog':
            case 'comment-note':
            case 'comment-picture':
            case 'comment-video':
            case 'comment-game':
                $this->view->setCaching(false); // Disable the cache since they are dynamic pages managed by the router
                $this->sAction = 'comment.inc';
                break;

            default:
                $this->displayPageNotFound(t('Sitemap Not Found!'));
        }
    }
}
