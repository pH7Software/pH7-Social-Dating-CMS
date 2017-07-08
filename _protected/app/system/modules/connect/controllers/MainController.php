<?php
/**
 * @title          Main Controller Class
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Controller
 * @version        1.0
 */

namespace PH7;

class MainController extends Controller
{

    /**
     * @access protected Protected access for the AdminController class derived from this class.
     * @var string $sTitle
     */
    protected $sTitle;

    private $_sApi, $_sUrl;


    public function index()
    {
        $this->sTitle = t('Welcome to Universal Login');
        $this->view->page_title = $this->sTitle;
        $this->view->meta_description = t('Universal Login, Use the service Facebook, Twitter, Outlook, Microsoft, Google, or other account to login on the social dating site %site_name%');
        $this->view->meta_keywords = t('connect, login, Register, universal login, Facebook, Twitter, Outlook, Microsoft, Google, social network, dating site, email');
        $this->view->h1_title = $this->sTitle;

        $this->output();

    }

    public function register()
    {
        $this->view->page_title = t('You are successfully registered!');
        $this->view->h4_title = t('Loading...');

        $this->design->setRedirect($this->_sUrl, null, null, 5);

        $this->manualTplInclude('waiting.inc.tpl');
        $this->output();

    }

    public function login($sApiName = '')
    {
        $this->_sApi = $sApiName;
        $this->_whatApi();

        $this->sTitle = t('Signing in...');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->design->setRedirect($this->_sUrl);

        $this->manualTplInclude('waiting.inc.tpl');
        $this->output();
    }

    public function home()
    {
        $this->sTitle = t('Loading...');
        $this->view->page_title = $this->sTitle;
        $this->view->h3_title = $this->sTitle;

        $this->design->setRedirect($this->registry->site_url);

        $this->manualTplInclude('waiting.inc.tpl');
        $this->output();
    }

    private function _whatApi()
    {
        switch ($this->_sApi)
        {
            case 'fb':
                if (!$this->config->values['module.api']['facebook.enabled']) continue;
                $this->_sUrl = new Facebook;
            break;

            case 'google':
                if (!$this->config->values['module.api']['google.enabled']) continue;
                $this->_sUrl = new Google($this->session, $this->httpRequest, $this->registry);
            break;

            case 'twitter':
                if (!$this->config->values['module.api']['twitter.enabled']) continue;
                $this->_sUrl = new Twitter;
            break;

            case 'microsoft':
                if (!$this->config->values['module.api']['microsoft.enabled']) continue;
                $this->_sUrl = new Microsoft;
            break;

            default:
                $this->displayPageNotFound(t('The %0% API is incorrect.', $this->_sApi));
        }
    }

}
