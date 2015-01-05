<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */
namespace PH7;

use PH7\Framework\Navigation\Page, PH7\Framework\Url\Header, PH7\Framework\Mvc\Router\Uri;

class SettingController extends Controller
{

    private $sTitle;

    public function index()
    {
        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'general'));
    }

    public function general()
    {
        // Add Css Style for Tabs
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'tabs.css');

        $this->sTitle = t('General Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function ads()
    {
        $oPage = new Page;
        $sTable = AdsCore::getTable();
        $iTotalAds = (new AdsCoreModel)->total($sTable);

        $this->view->total_pages = $oPage->getTotalPages($iTotalAds, 10);
        $this->view->current_page = $oPage->getCurrentPage();
        unset($oPage, $sTable);

        // Add JS file for the ads form
        $this->design->addJs(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS, 'common.js');

        $this->sTitle = t('Advertisement Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->h4_title = t('%0% Banners', $iTotalAds);
        $this->output();

    }

    public function addAds()
    {
        $this->sTitle = t('Set Advertisement');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function analyticsApi()
    {
        $this->sTitle = t('Analytics Api Code');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function style()
    {
        $this->sTitle = t('Style code injection');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function script()
    {
        $this->sTitle = t('JavaScript code injection');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function metaMain()
    {
        // divShow.js for the Change Language Menu
        $this->design->addJs(PH7_STATIC . PH7_JS, 'divShow.js');

        $this->sTitle = t('Meta Tags - Settings');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $aLangs = $this->file->getDirList(PH7_PATH_APP_LANG);
        if (!in_array(substr($this->httpRequest->currentUrl(), -5), $aLangs)) {
            Header::redirect(Uri::get(PH7_ADMIN_MOD, 'setting', 'metamain', PH7_LANG_NAME, false));
        }
        unset($aLangs);

        $this->output();
    }

    public function license()
    {
        $this->sTitle = t('License Key');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        if ($this->httpRequest->getExists('set_msg'))
        {
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

        switch (PH7_LICENSE_STATUS)
        {
            case 'active':
                $sMsg = t('Hurrah! Your License Key was saved successfully.');
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
