<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Translate\Lang;
use PH7\Framework\Url\Header;

class SettingController extends Controller
{
    private const ADS_PER_PAGE = 10;

    public function index(): void
    {
        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'setting', 'general')
        );
    }

    public function general(): void
    {
        // Add Css Style for Tabs
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'tabs.css'
        );

        $this->view->page_title = $this->view->h1_title = t('General Settings');
        $this->output();
    }

    public function resetColor(): void
    {
        if ((new SecurityToken)->checkUrl()) {
            $this->resetColorFields();

            $sMsg = t('Colors are successfully reset!');
            $sMsgType = Design::SUCCESS_TYPE;
        } else {
            $sMsg = Form::errorTokenMsg();
            $sMsgType = Design::ERROR_TYPE;
        }

        Header::redirect(
            Uri::get(PH7_ADMIN_MOD, 'setting', 'general') . '#p=design',
            $sMsg,
            $sMsgType
        );
    }

    public function ads(): void
    {
        $oPage = new Page;
        $sTable = AdsCore::getTable();
        $iTotalAds = (new AdsCoreModel)->total($sTable);

        $this->view->total_pages = $oPage->getTotalPages($iTotalAds, self::ADS_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        unset($oPage, $sTable);

        // Add JS file for the ads form
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'common.js'
        );

        $this->view->page_title = $this->view->h1_title = t('Banner Settings');
        $this->view->h4_title = nt('%n% Banner', '%n% Banners', $iTotalAds);
        $this->output();
    }

    public function addAds(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Add a New Banner');
        $this->output();
    }

    public function analyticsApi(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Analytics API Code');
        $this->output();
    }

    public function style(): void
    {
        $this->view->page_title = $this->view->h1_title = t('Style Code Injection');
        $this->output();
    }

    public function script(): void
    {
        $this->view->page_title = $this->view->h1_title = t('JavaScript Code Injection');
        $this->output();
    }

    public function metaMain(): void
    {
        // divShow.js for the Language Menu List
        $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');

        $this->view->page_title = $this->view->h1_title = t('Meta Tags - Settings');

        if ($this->langNameFromUrlDoesNotExist()) {
            Header::redirect(
                Uri::get(
                    PH7_ADMIN_MOD,
                    'setting',
                    'metamain',
                    PH7_LANG_NAME,
                    false
                )
            );
        }

        $this->output();
    }

    private function resetColorFields(): void
    {
        $aColorFields = [
            'backgroundColor',
            'textColor',
            'heading1Color',
            'heading2Color',
            'heading3Color',
            'linkColor',
            'footerLinkColor',
            'linkHoverColor'
        ];

        foreach ($aColorFields as $sFieldName) {
            DbConfig::setSetting('', $sFieldName);
        }

        DbConfig::clearCache();
    }

    /**
     * Check if the locale language name (eg "en_US") specified in the URL path exists in pH7Builder.
     */
    private function langNameFromUrlDoesNotExist(): bool
    {
        $aLangs = $this->file->getDirList(PH7_PATH_APP_LANG);

        return !in_array(
            substr($this->httpRequest->currentUrl(), -Lang::LANG_FOLDER_LENGTH),
            $aLangs,
            true
        );
    }
}
