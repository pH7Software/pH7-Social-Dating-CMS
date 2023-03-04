<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Xml / Controller
 */

declare(strict_types=1);

namespace PH7;

class MainController extends Controller
{
    protected const STATIC_CACHE_LIFETIME = 86400; // 86400 secs = 24 hours

    protected DataCoreModel $oDataModel;

    protected string $sTitle;

    protected string $sAction;

    protected string $sXmlType;

    public function __construct()
    {
        parent::__construct();

        $this->oDataModel = new DataCoreModel;

        // Enable caching for all pages of this module
        $this->enableStaticTplCache();
    }

    public function xslLayout(): void
    {
        $this->setContentType();
        $this->view->display('layout.xsl.tpl');
    }

    protected function xmlLink(): void
    {
        $this->setContentType();
    }

    /**
     * @param string $sAction
     * @param mixed (array, string, integer, ...) $mParam Default Type.
     *
     * @return void
     */
    protected function generateXmlRouter(string $sAction, $mParam = null): void
    {
        $this->view->members = $this->oDataModel->getProfiles();
        $this->view->blogs = $this->oDataModel->getBlogs();
        $this->view->notes = $this->oDataModel->getNotes();
        $this->view->forums = $this->oDataModel->getForums();
        $this->view->forums_topics = $this->oDataModel->getForumsTopics();
        $this->view->albums_pictures = $this->oDataModel->getAlbumsPictures();
        $this->view->pictures = $this->oDataModel->getPictures();
        $this->view->albums_videos = $this->oDataModel->getAlbumsVideos();
        $this->view->videos = $this->oDataModel->getVideos();

        // For the Comments
        $this->generateCommentRouter($sAction, $mParam);
    }

    protected function xmlOutput(): void
    {
        /* Compression damages the XML files, so disable them */
        $this->view->setHtmlCompress(false);
        $this->view->setPhpCompress(false);

        // Output
        $this->setContentType();
        $this->view->display($this->sAction . PH7_DOT . $this->sXmlType . '.xml.tpl');
    }

    protected function setContentType(): void
    {
        header('Content-Type: text/xml; charset=' . PH7_ENCODING);
    }

    /**
     * @param mixed $mParam
     *
     * @return bool
     */
    protected function isParamValid($mParam): bool
    {
        return !empty($mParam) && is_numeric($mParam);
    }

    /**
     * @param string $sAction
     * @param mixed $mParam
     *
     * @return void
     */
    private function generateCommentRouter(string $sAction, $mParam): void
    {
        switch ($sAction) {
            case 'comment-profile':
                $this->view->table = 'profile';
                $this->view->comments = $this->isParamValid($mParam) ? $this->oDataModel->getRecipientCommentsProfiles($mParam) : $this->view->comments = $this->oDataModel->getCommentsProfiles();
                break;

            case 'comment-blog':
                $this->view->table = 'blog';
                $this->view->comments = $this->isParamValid($mParam) ? $this->oDataModel->getRecipientCommentsBlogs($mParam) : $this->view->comments = $this->oDataModel->getCommentsBlogs();
                break;

            case 'comment-note':
                $this->view->table = 'note';
                $this->view->comments = $this->isParamValid($mParam) ? $this->oDataModel->getRecipientCommentsNotes($mParam) : $this->oDataModel->getCommentsNotes();
                break;

            case 'comment-picture':
                $this->view->table = 'picture';
                $this->view->comments = $this->isParamValid($mParam) ? $this->oDataModel->getRecipientCommentsPictures($mParam) : $this->oDataModel->getCommentsPictures();
                break;

            case 'comment-video':
                $this->view->table = 'video';
                $this->view->comments = $this->isParamValid($mParam) ? $this->oDataModel->getRecipientCommentsVideos($mParam) : $this->oDataModel->getCommentsVideos();
                break;
        }
    }

    private function enableStaticTplCache(): void
    {
        $this->view->setCaching(true);
        $this->view->setCacheExpire(self::STATIC_CACHE_LIFETIME);
    }
}
