<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Url\Header;

class SettingController extends Controller
{
    const ADS_PER_PAGE = 10;

    public function index()
    {
        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'general'));
    }

    public function general()
    {
        // Add Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tabs.css');

        $this->view->page_title = $this->view->h1_title = t('General Settings');
        $this->output();
    }

    public function ads()
    {
        $oPage = new Page;
        $sTable = AdsCore::getTable();
        $iTotalAds = (new AdsCoreModel)->total($sTable);

        $this->view->total_pages = $oPage->getTotalPages($iTotalAds, self::ADS_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        unset($oPage, $sTable);

        // Add JS file for the ads form
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'common.js');

        $this->view->page_title = $this->view->h1_title = t('Banner Settings');
        $this->view->h4_title = nt('%n% Banner', '%n% Banners', $iTotalAds);
        $this->output();

    }

    public function addAds()
    {
        $this->view->page_title = $this->view->h1_title = t('Add a New Banner');
        $this->output();
    }

    public function analyticsApi()
    {
        $this->view->page_title = $this->view->h1_title = t('Analytics API Code');
        $this->output();
    }

    public function style()
    {
        $this->view->page_title = $this->view->h1_title = t('Style Code Injection');
        $this->output();
    }

    public function script()
    {
        $this->view->page_title = $this->view->h1_title = t('JavaScript Code Injection');
        $this->output();
    }

    public function metaMain()
    {
        // divShow.js for the Language Menu List
        $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');

        $this->view->page_title = $this->view->h1_title = t('Meta Tags - Settings');

        $aLangs = $this->file->getDirList(PH7_PATH_APP_LANG);
        if (!in_array(substr($this->httpRequest->currentUrl(), -5), $aLangs)) {
            Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'metamain', PH7_LANG_NAME, false));
        }
        unset($aLangs);

        $this->output();
    }

    public function license()
    {
        $this->view->page_title = $this->view->h1_title = t('License Key');

        if ($this->httpRequest->getExists('set_msg')) {
            $aData = $this->_getLicStatusMsg();
            $this->design->setFlashMsg($aData['msg'], ($aData['is_err'] ? 'error' : 'success'));
        }

        $this->output();
    }

    /**
     * Get the status and the message for the license key.
     *
     * @access private
     * @return array ['is_err' => BOOLEAN, 'msg' => STRING];
     */
    private function _getLicStatusMsg()
    {
        $bIsErr = true; // Set default value

        switch (PH7_LICENSE_STATUS) {
            case 'active':
                $sMsg = t('Hurrah! Your License Key has been successfully enabled!');
                $bIsErr = false;
                break;

            case 'invalid':
                $sMsg = t('Oops! Your license key is Invalid.');
                break;

            case 'expired':
                $sMsg = t('Oops! Your license key is Expired.');
                break;

            case 'suspended':
                $sMsg = t('We are sorry, but your license key is Suspended.');
                break;

            default:
                $sMsg = t('Oops! We have received an invalid response from the server. Please try again later.');
        }

        return ['is_err' => $bIsErr, 'msg' => $sMsg];
    }
}
