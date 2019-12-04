<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Forum / Controller
 */

namespace PH7;

use PH7\Framework\Analytics\Statistic;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\Ban\Ban;
use PH7\Framework\Url\Header;
use Teapot\StatusCode;

class ForumController extends Controller
{
    const TOPICS_PER_PAGE = 20;
    const FORUMS_PER_PAGE = 20;
    const POSTS_PER_PAGE = 10;
    const MAX_SUMMARY_MESSAGE_LENGTH_SHOWN = 150;

    /** @var ForumModel */
    private $oForumModel;

    /** @var Page */
    private $oPage;

    /** @var string */
    private $sTitle;

    /** @var string */
    private $sMsg;

    /** @var int */
    private $iTotalTopics;

    public function __construct()
    {
        parent::__construct();

        $this->oForumModel = new ForumModel;
        $this->oPage = new Page;
        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->member_id = $this->session->get('member_id');

        // Predefined meta_keywords tags
        $this->view->meta_keywords = t('forum,discussion,dating forum,social forum,people,meet people,forums,free dating forum,free forum,community forum,social forum');

        // Adding Css Style for the Layout Forum
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'common.css'
        );
    }

    public function index()
    {
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oForumModel->totalForums(), self::FORUMS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $oCategories = $this->oForumModel->getCategory();
        $oForums = $this->oForumModel->getForum(
            null,
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oCategories) && empty($oForums)) {
            $this->sTitle = t('Nothing found!');
            $this->notFound();
        } else {
            $this->view->page_title = t('Discussion Forums - %site_name%');
            $this->view->meta_description = t('Discussion Forums, Social Network Site - %site_name%');
            $this->view->h1_title = t('Discussion Forums, Social Network Site');

            $this->view->categories = $oCategories;
            $this->view->forums = $oForums;
        }

        $this->output();
    }

    public function topic()
    {
        $sForumName = $this->httpRequest->get('forum_name');

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oForumModel->totalTopics(), self::TOPICS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();
        $oTopics = $this->oForumModel->getTopic(
            strstr($sForumName, '-', true),
            $this->httpRequest->get('forum_id', 'int'),
            null,
            null,
            null,
            '1',
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        $this->view->forum_name = $sForumName;
        $this->view->forum_id = $this->httpRequest->get('forum_id', 'int');

        if (empty($oTopics)) {
            $this->sTitle = t('No Topics found.');
            $this->notFound();
        } else {
            $this->view->page_title = t('%0% - Forums', $this->str->upperFirst($sForumName));
            $this->view->meta_description = t('%0% - Topics - Discussion Forums', $sForumName);
            $this->view->meta_keywords = t('%0%,forum,discussion,dating forum,social forum,people,meet people,forums,free dating forum', $this->getNameAsKeywords($sForumName));
            $this->view->h1_title = $this->str->upperFirst($sForumName);
            $this->view->topics = $oTopics;
        }

        $this->output();
    }

    public function post()
    {
        $oPost = $this->oForumModel->getTopic(
            strstr($this->httpRequest->get('forum_name'), '-', true),
            $this->httpRequest->get('forum_id', 'int'),
            strstr($this->httpRequest->get('topic_name'), '-', true),
            $this->httpRequest->get('topic_id', 'int'),
            null,
            '1',
            0,
            1
        );

        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->oForumModel->totalMessages(
                $this->httpRequest->get('topic_id', 'int')
            ),
            self::POSTS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();
        $oMessages = $this->oForumModel->getMessage(
            $this->httpRequest->get('topic_id', 'int'),
            null,
            null,
            '1',
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oPost)) {
            $this->sTitle = t('Topic Not Found!');
            $this->notFound();
        } else {
            $sForumName = $this->httpRequest->get('forum_name');

            // Adding the RSS link
            $this->view->header = '<link rel="alternate" type="application/rss+xml" title="' . t('Latest Forum Posts') . '" href="' . Uri::get('xml', 'rss', 'xmlrouter', 'forum-post,' . $oPost->topicId) . '" />';
            $this->view->page_title = t('%0% -> %1% - Forum', $this->str->upperFirst($sForumName), $this->getTitle($oPost->title));
            $this->view->meta_description = t('%0% Topics - Discussion Forums', $this->getShortedMessage($oPost->message));

            // Generates beautiful meta keywords for good SEO
            $this->view->meta_keywords = t('%0%,%1%,forum,discussion,dating forum,social forum', $this->getNameAsKeywords($sForumName), $this->getTitleAsKeywords($oPost->title));
            $this->view->h1_title = $this->getTitle($oPost->title);

            $this->view->dateTime = $this->dateTime;
            $this->view->post = $oPost;
            $this->view->messages = $oMessages;

            // Set Topics Views Statistics
            Statistic::setView($oPost->topicId, DbTableName::FORUM_TOPIC);
        }

        $this->output();
    }

    public function showPostByProfile()
    {
        $sUsername = $this->httpRequest->get('username');
        $this->view->username = $sUsername;

        $iProfileId = (new UserCoreModel)->getId(null, $sUsername);

        $this->iTotalTopics = $this->oForumModel->totalTopics(null, $iProfileId);
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalTopics, self::TOPICS_PER_PAGE
        );
        $this->view->current_page = $this->oPage->getCurrentPage();

        $this->view->topic_number = nt('%n% Topic:', '%n% Topics:', $this->iTotalTopics);

        $oTopics = $this->oForumModel->getPostByProfile(
            $iProfileId,
            '1',
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );
        if (empty($oTopics)) {
            $this->sTitle = t("%0% doesn't have any posts yet.", $sUsername);
            $this->notFound(false); // Because the Ajax blocks profile, we can not put HTTP error code 404, so the attribute is "false"
        } else {
            $this->sTitle = t("%0%'s Forum Posts", $sUsername);
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->topics = $oTopics;
        }

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Forum Search - Looking for a Forum Post | %site_name%');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('Topic Search - Discussion Forum - %site_name%');
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function result()
    {
        $this->iTotalTopics = $this->oForumModel->search(
            $this->httpRequest->get('looking'),
            true,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            null,
            null
        );
        $this->view->total_pages = $this->oPage->getTotalPages(
            $this->iTotalTopics, self::POSTS_PER_PAGE
        );

        $this->view->current_page = $this->oPage->getCurrentPage();

        $oSearch = $this->oForumModel->search(
            $this->httpRequest->get('looking'),
            false,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            $this->oPage->getFirstItem(),
            $this->oPage->getNbItemsPerPage()
        );

        if (empty($oSearch)) {
            $this->sTitle = t('Sorry, Your search returned no results!');
            $this->notFound();
        } else {
            $this->sTitle = t('Forums - Your search returned');
            $this->view->page_title = $this->sTitle;
            $this->view->h3_title = nt('%n% Result', '%n% Results', $this->iTotalTopics);
            $this->view->meta_description = t('Search - Discussion Forum');
            $this->view->meta_keywords = t('search,forum,forums,discussion forum');
            $this->view->h2_title = $this->sTitle;
            $this->view->topics = $oSearch;
        }

        $this->manualTplInclude('topic.tpl');

        $this->output();
    }

    public function addTopic()
    {
        $this->sTitle = t('Add a new Topic');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function editTopic()
    {
        $this->sTitle = t('Edit Topic');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function editMessage()
    {
        $this->sTitle = t('Edit your Message');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function reply()
    {
        $this->sTitle = t('Reply Message');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function deleteTopic()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iTopicId = (int)$aData[0];
        $iForumId = (int)$aData[1];
        $sForumName = (string)$aData[2];

        if ($this->oForumModel->deleteTopic($this->session->get('member_id'), $iTopicId)) {
            $this->sMsg = t('Your topic has been deleted.');
        } else {
            $this->sMsg = t('Oops! Your topic could not be deleted.');
        }

        Header::redirect(
            Uri::get('forum', 'forum', 'topic', $sForumName . ',' . $iForumId),
            $this->sMsg
        );
    }

    public function deleteMessage()
    {
        $aData = explode('_', $this->httpRequest->post('id'));
        $iMessageId = (int)$aData[0];
        $iTopicId = (int)$aData[1];
        $iForumId = (int)$aData[2];
        $sTopicTitle = (string)$aData[3];
        $sForumName = (string)$aData[4];
        unset($aData);

        if ($this->oForumModel->deleteMessage($this->session->get('member_id', 'int'), $iMessageId)) {
            $this->sMsg = t('Your message has been deleted.');
        } else {
            $this->sMsg = t('Oops! Your message could not be deleted.');
        }

        Header::redirect(
            Uri::get(
                'forum',
                'forum',
                'post',
                $sForumName . ',' . $iForumId . ',' . $sTopicTitle . ',' . $iTopicId
            ),
            $this->sMsg
        );
    }

    /**
     * @param string $sForumName
     *
     * @return string
     */
    private function getNameAsKeywords($sForumName)
    {
        return str_replace(' ', ',', $sForumName);
    }

    /**
     * @param string $sTitle
     *
     * @return string
     */
    private function getTitleAsKeywords($sTitle)
    {
        return str_replace(' ', ',', Ban::filterWord($sTitle, false));
    }

    /**
     * @param string $sTitle
     *
     * @return string
     */
    private function getTitle($sTitle)
    {
        return $this->str->escape(Ban::filterWord($sTitle), true);
    }

    /**
     * @param string $sMessage
     *
     * @return string
     */
    private function getShortedMessage($sMessage)
    {
        return substr(
            $this->str->escape(Ban::filterWord($sMessage), true),
            0,
            self::MAX_SUMMARY_MESSAGE_LENGTH_SHOWN
        );
    }

    /**
     * Set a Not Found Error Message with HTTP 404 Code Status.
     *
     * @param bool $b404Status For the Ajax blocks profile, we can not put HTTP error code 404, so the attribute must be set to "false".
     *
     * @return void
     */
    private function notFound($b404Status = true)
    {
        if ($b404Status === true) {
            Http::setHeadersByCode(StatusCode::NOT_FOUND);
        }

        $sErrMsg = '';
        if ($b404Status === true) {
            $sForumHomepageUrl = Uri::get('forum', 'forum', 'index');
            $sErrMsg = '<br />' . t('Please return to the <a href="%0%">main forum page</a> or <a href="%1%">the previous page</a>.', $sForumHomepageUrl, 'javascript:history.back();');
        }

        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->view->error = $this->sTitle . $sErrMsg;
    }
}
