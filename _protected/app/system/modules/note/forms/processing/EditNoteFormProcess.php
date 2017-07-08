<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Form / Processing
 */

namespace PH7;

defined('PH7') or die('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;
use stdClass;

class EditNoteFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oNote = new Note;
        $oNoteModel = new NoteModel;
        $iNoteId = $this->httpRequest->get('id');
        $sPostId = $oNoteModel->getPostId($iNoteId);
        $sUsername = $this->session->get('member_username');
        $iProfileId = $this->session->get('member_id');
        $oPost = $oNoteModel->readPost($sPostId, $iProfileId);

        /*** Updating the ID of the post if it has changed ***/
        $sPostId = $this->str->lower($this->httpRequest->post('post_id'));
        if (!$this->str->equals($sPostId, $oPost->postId)) {
            if ($oNote->checkPostId($sPostId, $iProfileId, $oNoteModel)) {
                $oNoteModel->updatePost('postId', $sPostId, $iNoteId, $iProfileId);
            } else {
                \PFBC\Form::setError('form_note', t('The post ID already exists or is incorrect.'));
                return;
            }
        }

        if (!$this->updateCategories($iNoteId, $iProfileId, $oPost, $oNoteModel)) {
            \PFBC\Form::setError('form_note', t('You cannot select more than %0% categories.', Note::MAX_CATEGORY_ALLOWED));
            return; // Stop execution of the method
        }

        // Thumbnail
        $oNote->setThumb($oPost, $oNoteModel, $this->file);

        if (!$this->str->equals($this->httpRequest->post('title'), $oPost->title))
            $oNoteModel->updatePost('title', $this->httpRequest->post('title'), $iNoteId, $iProfileId);

        // HTML contents, So we use Http::ONLY_XSS_CLEAN constant
        if (!$this->str->equals($this->httpRequest->post('content', Http::ONLY_XSS_CLEAN), $oPost->content))
            $oNoteModel->updatePost('content', $this->httpRequest->post('content', Http::ONLY_XSS_CLEAN), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('lang_id'), $oPost->langId))
            $oNoteModel->updatePost('langId', $this->httpRequest->post('lang_id'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('slogan'), $oPost->slogan))
            $oNoteModel->updatePost('slogan', $this->httpRequest->post('slogan'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('tags'), $oPost->tags))
            $oNoteModel->updatePost('tags', $this->httpRequest->post('tags'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('page_title'), $oPost->pageTitle))
            $oNoteModel->updatePost('pageTitle', $this->httpRequest->post('page_title'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('meta_description'), $oPost->metaDescription))
            $oNoteModel->updatePost('metaDescription', $this->httpRequest->post('meta_description'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('meta_keywords'), $oPost->metaKeywords))
            $oNoteModel->updatePost('metaKeywords', $this->httpRequest->post('meta_keywords'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('meta_robots'), $oPost->metaRobots))
            $oNoteModel->updatePost('metaRobots', $this->httpRequest->post('meta_robots'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('meta_author'), $oPost->metaAuthor))
            $oNoteModel->updatePost('metaAuthor', $this->httpRequest->post('meta_author'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('meta_copyright'), $oPost->metaCopyright))
            $oNoteModel->updatePost('metaCopyright', $this->httpRequest->post('meta_copyright'), $iNoteId, $iProfileId);

        if (!$this->str->equals($this->httpRequest->post('enable_comment'), $oPost->enableComment))
            $oNoteModel->updatePost('enableComment', $this->httpRequest->post('enable_comment'), $iNoteId, $iProfileId);

        // Updated the approved status
        $iApproved = (DbConfig::getSetting('noteManualApproval') == 0) ? '1' : '0';
        $oNoteModel->updatePost('approved', $iApproved, $iNoteId, $iProfileId);

        // Updated the modification Date
        $oNoteModel->updatePost('updatedDate', $this->dateTime->get()->dateTime('Y-m-d H:i:s'), $iNoteId, $iProfileId);
        unset($oNote, $oNoteModel);

        Note::clearCache();

        if ($iApproved == '0') {
            $sMsg = t('Your updated note has been received. It will not be visible until it is approved by our moderators. Please do not send a new one.');
        } else {
            $sMsg = t('Post successfully updated!');
        }

        Header::redirect(Uri::get('note', 'main', 'read', $sUsername . ',' . $sPostId), $sMsg);
    }

    /**
     * Update categories.
     *
     * @param integer $iNoteId
     * @param integer $iProfileId
     * @param stdClass $oPost Post data from the database
     * @param NoteModel $oNoteModel
     * @return boolean FALSE if the maximal number of categories allowed has been reached, FALSE otherwise.
     *
     * @internal WARNING: Be careful, you should use Http::NO_CLEAN constant,
     * otherwise Http::post() method removes the special tags and damages the SET function SQL for entry into the database.
     */
    protected function updateCategories($iNoteId, $iProfileId, stdClass $oPost, NoteModel $oNoteModel)
    {
        if (!$this->str->equals($this->httpRequest->post('category_id', Http::NO_CLEAN), $oPost->categoryId)) {
            if (count($this->httpRequest->post('category_id', Http::NO_CLEAN)) > Note::MAX_CATEGORY_ALLOWED) {
                return false;
            }

            $oNoteModel->deleteCategory($iNoteId);

            foreach ($this->httpRequest->post('category_id', Http::NO_CLEAN) as $iCategoryId) {
                $oNoteModel->addCategory($iCategoryId, $iNoteId, $iProfileId);
            }
        }
        return true;
    }
}
