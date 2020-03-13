<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\CKEditor;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Textbox;
use PFBC\Element\Token;
use PFBC\Validation\Str;
use PH7\Framework\File\File;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;

class MetaMainForm
{
    public static function display()
    {
        if (isset($_POST['submit_meta'])) {
            if (\PFBC\Form::isValid($_POST['submit_meta'])) {
                new MetaMainFormProcess;
            }

            Header::redirect();
        }

        $sWhereLang = (new Http)->get('meta_lang');
        $oMeta = DbConfig::getMetaMain($sWhereLang);

        $oForm = new \PFBC\Form('form_meta');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_meta', 'form_meta'));
        $oForm->addElement(new Token('admin_meta'));

        self::generateLanguageSwitchField($oForm);

        $oForm->addElement(new Textbox(t('Language:'), 'lang_id', ['disabled' => 'disabled', 'value' => $oMeta->langId]));

        $oForm->addElement(new Textbox(t('Home page title:'), 'page_title', ['value' => $oMeta->pageTitle, 'validation' => new Str(2, 100), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Headline:'), 'headline', ['description' => t('Right headline mainly displaying on the visitors homepage'), 'value' => $oMeta->headline, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Slogan:'), 'slogan', ['description' => t('Left slogan (headline) mainly displaying on the visitors homepage'), 'value' => $oMeta->slogan, 'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'required' => 1]));

        $oForm->addElement(new CKEditor(t('SEO text:'), 'promo_text', ['description' => t('Promotional text displaying on the visitors homepage.'), 'value' => $oMeta->promoText, 'required' => 1]));

        $oForm->addElement(new Textbox(t('Description (meta tag):'), 'meta_description', ['value' => $oMeta->metaDescription, 'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Keywords (meta tag):'), 'meta_keywords', ['description' => t('Separate keywords by commas.'), 'value' => $oMeta->metaKeywords, 'validation' => new Str(Form::MIN_STRING_FIELD_LENGTH, Form::MAX_STRING_FIELD_LENGTH), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Robots (meta tag):'), 'meta_robots', ['value' => $oMeta->metaRobots, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Author (meta tag):'), 'meta_author', ['value' => $oMeta->metaAuthor, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Copyright (meta tag):'), 'meta_copyright', ['value' => $oMeta->metaCopyright, 'validation' => new Str(2, 55), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Rating (meta tag):'), 'meta_rating', ['value' => $oMeta->metaRating, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Distribution (meta tag):'), 'meta_distribution', ['value' => $oMeta->metaDistribution, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Textbox(t('Category (meta tag):'), 'meta_category', ['value' => $oMeta->metaCategory, 'validation' => new Str(2, 50), 'required' => 1]));

        $oForm->addElement(new Button);

        $oForm->render();
    }

    /**
     * Generate the list of languages that allows admins to switch to another language on the meta tags form.
     *
     * @param \PFBC\Form $oForm
     *
     * @return void
     */
    private static function generateLanguageSwitchField(\PFBC\Form $oForm)
    {
        $aLangs = (new File)->getDirList(PH7_PATH_APP_LANG);
        $iTotalLangs = count($aLangs);
        if ($iTotalLangs > 1) {
            $oForm->addElement(new HTMLExternal('<div class="center divShow">'));
            $oForm->addElement(new HTMLExternal('<h3 class="underline"><a href="#showDiv_listLang" title="' . t('Click here to show/hide the languages') . '">' . t('Change language for the Meta Tags') . '</a></h3>'));
            $oForm->addElement(new HTMLExternal('<ul class="hidden" id="showDiv_listLang">'));

            for ($iLangIndex = 0; $iLangIndex < $iTotalLangs; $iLangIndex++) {
                $sAbbrLang = Lang::getIsoCode($aLangs[$iLangIndex]);
                $oForm->addElement(new HTMLExternal('<li>' . ($iLangIndex + 1) . ') ' . '<a class="bold" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'metamain', $aLangs[$iLangIndex], false) . '" title="' . t($sAbbrLang) . '">' . t($sAbbrLang) . ' (' . $aLangs[$iLangIndex] . ')</a></li>'));
            }
            $oForm->addElement(new HTMLExternal('</ul></div>'));
        }
        unset($aLangs);
    }
}
