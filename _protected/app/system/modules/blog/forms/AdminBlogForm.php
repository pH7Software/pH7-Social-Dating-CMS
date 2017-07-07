<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Blog / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class AdminBlogForm
{
    public static function display()
    {
        if (isset($_POST['submit_blog'])) {
            if (\PFBC\Form::isValid($_POST['submit_blog'])) {
                new AdminBlogFormProcess();
            }
            Header::redirect();
        }

        $oCategoryData = (new BlogModel)->getCategory(null, 0, 300);

        $aCategoryNames = array();
        foreach ($oCategoryData as $oId) {
            $aCategoryNames[$oId->categoryId] = $oId->name;
        }

        $oForm = new \PFBC\Form('form_blog');
        $oForm->configure(array('action' => ''));
        $oForm->addElement(new \PFBC\Element\Hidden('submit_blog', 'form_blog'));
        $oForm->addElement(new \PFBC\Element\Token('blog'));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Article name:'), 'title', array('validation' => new \PFBC\Validation\Str(2, 60), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Article ID:'), 'post_id', array('description' => Uri::get('blog', 'main', 'index') . '/<strong><span class="your-address">' . t('your-address') . '</span><span class="post_id"></span></strong>', 'title' => t('Article ID will be the name of the URL.'), 'id' => 'post_id', 'validation' => new \PFBC\Validation\Str(2, 60), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="label_flow">'));
        $oForm->addElement(new \PFBC\Element\Checkbox(t('Categories:'), 'category_id', $aCategoryNames, array('description' => t('Select a category that fits the best for your article.'), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\HTMLExternal('</div>'));
        $oForm->addElement(new \PFBC\Element\CKEditor(t('Body:'), 'content', array('validation' => new \PFBC\Validation\Str(30), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('The language of the article:'), 'lang_id', array('description' => t('e.g., "en", "fr", "es", "js"'), 'pattern' => '[a-z]{2}', 'validation' => new \PFBC\Validation\Str(2, 2), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Slogan:'), 'slogan', array('validation' => new \PFBC\Validation\Str(2, 200))));
        $oForm->addElement(new \PFBC\Element\File(t('Thumbnail:'), 'thumb', array('accept' => 'image/*')));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Tags:'), 'tags', array('description' => t('Separate keywords by commas and without spaces between the commas.'), 'validation' => new \PFBC\Validation\Str(2, 200))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Title (meta tag):'), 'page_title', array('validation' => new \PFBC\Validation\Str(2, 100), 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Description (meta tag):'), 'meta_description', array('validation' => new \PFBC\Validation\Str(2, 200))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Keywords (meta tag):'), 'meta_keywords', array('description' => t('Separate keywords by commas.'), 'validation' => new \PFBC\Validation\Str(2, 200))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Robots (meta tag):'), 'meta_robots', array('validation' => new \PFBC\Validation\Str(2, 50))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Author (meta tag):'), 'meta_author', array('validation' => new \PFBC\Validation\Str(2, 50))));
        $oForm->addElement(new \PFBC\Element\Textbox(t('Copyright (meta tag):'), 'meta_copyright', array('validation' => new \PFBC\Validation\Str(2, 50))));
        $oForm->addElement(new \PFBC\Element\Radio(t('Enable Comment:'), 'enable_comment', array('1' => t('Enable'), '0' => t('Disable')), array('value' => '1', 'required' => 1)));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_TPL_SYS_MOD . 'blog/' . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS . 'common.js"></script>'));
        $oForm->render();
    }
}
