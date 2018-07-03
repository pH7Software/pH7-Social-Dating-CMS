<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / From
 */

namespace PH7;

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
        $oForm->addElement(new \PFBC\Element\Hidden('submit_meta', 'form_meta'));
        $oForm->addElement(new \PFBC\Element\Token('admin_meta'));

        // Generate the list of languages
        $aLangs = (new File)->getDirList(PH7_PATH_APP_LANG);
        $iTotalLangs = count($aLangs);
        if ($iTotalLangs > 1) {
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<div class="center divShow">'));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<h3 class="underline"><a href="#showDiv_listLang" title="' . t('Click here to show/hide the languages') . '">' . t('Change language for the Meta Tags') . '</a></h3>'));
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<ul class="hidden" id="showDiv_listLang">'));

            for ($iLangIndex = 0; $iLangIndex < $iTotalLangs; $iLangIndex++) {
                $sAbbrLang = Lang::getIsoCode($aLangs[$iLangIndex]);
                $oForm->addElement(new \PFBC\Element\HTMLExternal('<li>' . ($iLangIndex + 1) . ') ' . '<a class="bold" href="' . Uri::get(PH7_ADMIN_MOD, 'setting', 'metamain', $aLangs[$iLangIndex], false) . '" title="' . t($sAbbrLang) . '">' . t($sAbbrLang) . ' (' . $aLangs[$iLangIndex] . ')</a></li>'));
            }
            $oForm->addElement(new \PFBC\Element\HTMLExternal('</ul></div>'));
        }
        unset($aLangs);

        $oForm->addElement(new \PFBC\Element\Textbox(t('Language:'), 'lang_id', ['disabled' => 'disabled', 'value' => $oMeta->langId]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Home page title:'), 'page_title', ['value' => $oMeta->pageTitle, 'validation' => new \PFBC\Validation\Str(2, 100), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Headline:'), 'headline', ['description' => t('Right headline mainly displaying on the visitors homepage'), 'value' => $oMeta->headline, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Slogan:'), 'slogan', ['description' => t('Left slogan (headline) mainly displaying on the visitors homepage'), 'value' => $oMeta->slogan, 'validation' => new \PFBC\Validation\Str(2, 200), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\CKEditor(t('SEO text:'), 'promo_text', ['description' => t('Promotional text displaying on the visitors homepage.'), 'value' => $oMeta->promoText, 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Description (meta tag):'), 'meta_description', ['value' => $oMeta->metaDescription, 'validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Keywords (meta tag):'), 'meta_keywords', ['description' => t('Separate keywords by commas.'), 'value' => $oMeta->metaKeywords, 'validation' => new \PFBC\Validation\Str(2, 255), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Robots (meta tag):'), 'meta_robots', ['value' => $oMeta->metaRobots, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Author (meta tag):'), 'meta_author', ['value' => $oMeta->metaAuthor, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Copyright (meta tag):'), 'meta_copyright', ['value' => $oMeta->metaCopyright, 'validation' => new \PFBC\Validation\Str(2, 55), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Rating (meta tag):'), 'meta_rating', ['value' => $oMeta->metaRating, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Distribution (meta tag):'), 'meta_distribution', ['value' => $oMeta->metaDistribution, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Textbox(t('Category (meta tag):'), 'meta_category', ['value' => $oMeta->metaCategory, 'validation' => new \PFBC\Validation\Str(2, 50), 'required' => 1]));

        $oForm->addElement(new \PFBC\Element\Button);

        $oForm->render();
    }
}
