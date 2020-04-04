<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Note / Form
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Checkbox;
use PFBC\Element\CKEditor;
use PFBC\Element\File;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Radio;
use PFBC\Element\Textbox;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Session\Session;
use PH7\Framework\Str\Str;
use PH7\Framework\Url\Header;

class EditNoteForm
{
    const MAX_CATEGORIES = 300;

    public static function display()
    {
        if (isset($_POST['submit_edit_note'])) {
            if (\PFBC\Form::isValid($_POST['submit_edit_note'])) {
                new EditNoteFormProcess();
            }

            Header::redirect();
        }

        // Generate edit form post of the note
        $oNoteModel = new NoteModel;

        $iNoteId = (new Http)->get('id', 'int');
        $iProfileId = (new Session)->get('member_id');
        $sPostId = $oNoteModel->getPostId($iNoteId);
        $oPost = $oNoteModel->readPost($sPostId, $iProfileId);

        if (!empty($oPost) && (new Str)->equals($iNoteId, (int)$oPost->noteId)) {
            $oCategoryData = $oNoteModel->getCategory(
                null,
                0,
                self::MAX_CATEGORIES
            );

            $aCategoryNames = [];
            foreach ($oCategoryData as $oCategory) {
                $aCategoryNames[$oCategory->categoryId] = $oCategory->name;
            }

            $aSelectedCategories = [];
            $oCategoryIds = $oNoteModel->getCategory(
                $iNoteId,
                0,
                self::MAX_CATEGORIES
            );
            unset($oNoteModel);

            foreach ($oCategoryIds as $oCategory) {
                $aSelectedCategories[] = $oCategory->categoryId;
            }

            $oForm = new \PFBC\Form('form_edit_note');
            $oForm->configure(['action' => '']);
            $oForm->addElement(new Hidden('submit_edit_note', 'form_edit_note'));
            $oForm->addElement(new \PFBC\Element\Token('edit_note'));
            $oForm->addElement(
                new Textbox(
                    t('Article name:'),
                    'title',
                    [
                        'value' => $oPost->title,
                        'validation' => new \PFBC\Validation\Str(2, 60),
                        'required' => 1
                    ]
                )
            );
            $oForm->addElement(
                new Textbox(
                    t('Article ID:'),
                    'post_id',
                    [
                        'value' => $oPost->postId,
                        'description' => Uri::get('note', 'main', 'read', (new Session)->get('member_username')) . '/<strong><span class="your-address">' . $oPost->postId . '</span><span class="post_id"></span></strong>',
                        'title' => t('Article ID will be the name of the URL.'),
                        'data-profile_id' => $iProfileId,
                        'id' => 'post_id',
                        'validation' => new \PFBC\Validation\Str(2, 50),
                        'required' => 1
                    ]
                )
            );
            $oForm->addElement(new HTMLExternal('<div class="label_flow">'));
            $oForm->addElement(new Checkbox(t('Categories:'), 'category_id', $aCategoryNames, ['description' => t('Select a category that fits the best for your article. You can select up to three different categories'), 'value' => $aSelectedCategories, 'required' => 1]));
            $oForm->addElement(new HTMLExternal('</div>'));
            $oForm->addElement(new CKEditor(t('Body:'), 'content', ['value' => $oPost->content, 'validation' => new \PFBC\Validation\Str(30), 'required' => 1]));
            $oForm->addElement(new Textbox(t('Language of your post:'), 'lang_id', ['value' => $oPost->langId, 'description' => t('e.g., "en", "fr", "es", "js"'), 'pattern' => '[a-z]{2}', 'validation' => new \PFBC\Validation\Str(2, 2), 'required' => 1]));
            $oForm->addElement(new Textbox(t('Slogan:'), 'slogan', ['value' => $oPost->slogan, 'validation' => new \PFBC\Validation\Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH)]));
            $oForm->addElement(new File(t('Thumbnail:'), 'thumb', ['accept' => 'image/*']));

            if (!empty($oPost->thumb)) {
                $oForm->addElement(new HTMLExternal('<p><br /><img src="' . PH7_URL_DATA_SYS_MOD . 'note/' . PH7_IMG . $oPost->username . PH7_SH . $oPost->thumb . '" alt="' . t('Thumbnail') . '" title="' . t('The current thumbnail of your post.') . '" class="avatar" /></p>'));
                $oForm->addElement(new HTMLExternal('<a href="' . Uri::get('note', 'main', 'removethumb', $oPost->noteId . (new Token)->url(), false) . '">' . t('Remove this thumbnail?') . '</a>'));
            }

            $oForm->addElement(new Textbox(t('Tags:'), 'tags', ['value' => $oPost->tags, 'description' => t('Separate keywords by commas and without spaces between the commas.'), 'validation' => new \PFBC\Validation\Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH)]));
            $oForm->addElement(new Textbox(t('Title (meta tag):'), 'page_title', ['value' => $oPost->pageTitle, 'validation' => new \PFBC\Validation\Str(2, 100), 'required' => 1]));
            $oForm->addElement(new Textbox(t('Description (meta tag):'), 'meta_description', ['validation' => new \PFBC\Validation\Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'value' => $oPost->metaDescription]));
            $oForm->addElement(new Textbox(t('Keywords (meta tag):'), 'meta_keywords', ['description' => t('Separate keywords by commas and without spaces between the commas.'), 'validation' => new \PFBC\Validation\Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'value' => $oPost->metaKeywords]));
            $oForm->addElement(new Textbox(t('Robots (meta tag):'), 'meta_robots', ['validation' => new \PFBC\Validation\Str(2, 50), 'value' => $oPost->metaRobots]));
            $oForm->addElement(new Textbox(t('Author (meta tag):'), 'meta_author', ['validation' => new \PFBC\Validation\Str(2, 50), 'value' => $oPost->metaAuthor]));
            $oForm->addElement(new Textbox(t('Copyright (meta tag):'), 'meta_copyright', ['validation' => new \PFBC\Validation\Str(2, 50), 'value' => $oPost->metaCopyright]));
            $oForm->addElement(new Radio(t('Enable Comment:'), 'enable_comment', ['1' => t('Enable'), '0' => t('Disable')], ['value' => $oPost->enableComment, 'required' => 1]));
            $oForm->addElement(new Button);
            $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_TPL_SYS_MOD . 'note/' . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS . 'common.js"></script>'));
            $oForm->render();
        } else {
            echo '<p class="center bold">' . t('Post Not Found!') . '</p>';
        }
    }
}
