<?php
/**
 * @title          Main Controller Class
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Connect / Controller
 */

namespace PH7;

class MainController extends Controller
{
    const FB_PROVIDER = 'fb';
    const GOOGLE_PROVIDER = 'google';
    const TWITTER_PROVIDER = 'twitter';
    const MICROSOFT_PROVIDER = 'microsoft';

    const REDIRECTION_DELAY = 5; // In secs

    /**
     * @internal Protected access for the AdminController class derived from this class.
     *
     * @var string $sTitle
     */
    protected $sTitle;

    /** @var string */
    private $sApi;

    /** @var string */
    private $sUrl;

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

        $this->design->setRedirect($this->sUrl, null, null, self::REDIRECTION_DELAY);

        $this->manualTplInclude('waiting.inc.tpl');
        $this->output();
    }

    public function login($sApiName = '')
    {
        $this->sApi = $sApiName;
        $this->whatApi();

        $this->sTitle = t('Signing in...');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->design->setRedirect($this->sUrl);

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

    /**
     * @internal API class (e.g. Facebook, Google, Twitter) uses "__toString" magic for returning the URL.
     */
    private function whatApi()
    {
        switch ($this->sApi) {
            case self::FB_PROVIDER:
                if (!$this->config->values['module.api']['facebook.enabled']) continue;
                $this->sUrl = new Facebook;
                break;

            case self::GOOGLE_PROVIDER:
                if (!$this->config->values['module.api']['google.enabled']) continue;
                $this->sUrl = new Google($this->session, $this->httpRequest, $this->registry);
                break;

            case self::TWITTER_PROVIDER:
                if (!$this->config->values['module.api']['twitter.enabled']) continue;
                $this->sUrl = new Twitter;
                break;

            case self::MICROSOFT_PROVIDER:
                if (!$this->config->values['module.api']['microsoft.enabled']) continue;
                $this->sUrl = new Microsoft;
                break;

            default:
                $this->displayPageNotFound(t('The %0% API is incorrect.', $this->sApi));
        }
    }
}
