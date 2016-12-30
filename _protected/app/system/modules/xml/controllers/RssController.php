<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Xml / Controller
 */
namespace PH7;
use PH7\Framework\Xml\Link;

class RssController extends MainController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->sTitle = t('RSS Feed List');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('RSS Feed %site_name%, Free Onlide Dating Site with Webcam Chat Rooms, Meet Single People with %site_name%');
        $this->view->h1_title = $this->sTitle;

        /*** Get the links ***/
        $sUrl = Framework\Mvc\Router\Uri::get('xml','rss','xmllink');
        $this->view->urls = (new Link($sUrl))->get();
        $this->output();
    }

    public function xmlLink()
    {
        parent::xmlLink();
        $this->view->display('rss_links.xml.tpl');
    }

    public function xmlRouter()
    {
        $sAction = $this->httpRequest->get('action');
        $mParam = $this->httpRequest->get('param');
        $this->_xmlRouter($sAction, $mParam);
        $this->sXmlType = 'rss';
        $this->view->current_date = DateFormat::getRss(); // Date format for RSS feed

        // RSS router
        switch ($sAction)
        {
            case 'blog':
            case 'note':
            case 'forum-topic':
                $this->sAction = $sAction;
            break;

            case 'comment-profile':
            case 'comment-blog':
            case 'comment-note':
            case 'comment-picture':
            case 'comment-video':
            case 'comment-game':
                $this->view->setCaching(false); // We disable the cache since they are dynamic pages managed by the router.
                $this->sAction = 'comment.inc';
            break;

            case 'forum-post' && !empty($mParam) && is_numeric($mParam):
                $this->view->setCaching(false); // We disable the cache since they are dynamic pages managed by the router.
                $this->view->forums_messages = $this->oDataModel->getForumsMessages($mParam);
                $this->sAction = $sAction;
            break;

            default:
                $this->displayPageNotFound(t('Not Found RSS Feed!'));
        }

        $this->xmlOutput();
    }

}
