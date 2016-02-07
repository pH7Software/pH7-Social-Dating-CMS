<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

use PH7\Framework\Mobile\MobApp, PH7\Framework\Mvc\Model\DbConfig;

class MainController extends Controller
{

    const GUEST_SPLASH_FILE = 'index.guest_splash', GUEST_FILE = 'index.guest';


    private $_sTitle, $_bIsMobApp;

    /**
     * Displaying the main homepage of the website.
     */
    public function index()
    {
        // We must not put the title as this is the homepage, so this is the default title is used.

        // For Profiles Carousel
        $this->view->userDesignModel = new UserDesignCoreModel;
        $this->view->userDesign = new UserDesignCore;

        // Only visitors
        if (!UserCore::auth())
        {
            // To check if the site is called by a mobile native app
            $this->_bIsMobApp = $this->view->is_mobapp = MobApp::is();

            if ($this->_getGuestTplPage() === static::GUEST_SPLASH_FILE)
            {
                $bIsBgVideo = (bool) DbConfig::getSetting('bgSplashVideo');

                // Assign the background video option (this tpl var is only available in index.guest_splash.tpl)
                $this->view->is_bg_video = $bIsBgVideo;
            }

            $sIsCssVidSplashFile = (!empty($bIsBgVideo) && $bIsBgVideo) ? 'video_splash.css,' : '';

            // Set CSS and JS files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, $sIsCssVidSplashFile . 'splash.css,tooltip.css,js/jquery/carousel.css');
            $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'jquery/carouFredSel.js,' . PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS . 'splash.js');

            // Assigns the promo text to the view
            $this->view->promo_text = DbConfig::getMetaMain(PH7_LANG_NAME)->promoText;

            $this->manualTplInclude($this->_getGuestTplPage() . '.inc.tpl');
        }
        elseif (UserCore::auth()) // Only for Members
        {
            // Set CSS and JS files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'zoomer.css');
            $this->design->addJs(PH7_STATIC . PH7_JS, 'zoomer.js,Wall.js');

            // Assigns the user's first name to the view for the Welcome Message
            $this->view->first_name = $this->session->get('member_first_name');

            $this->manualTplInclude('index.user.inc.tpl');
        }
        $this->output();
    }

    public function login()
    {
        // Display Sign In page
        $this->_sTitle = t('Sign In to %site_name%');
        $this->view->page_title = $this->_sTitle;
        $this->view->h1_title = $this->_sTitle;
        $this->output();
    }

    public function resendActivation()
    {
        // Display Resend Activation page
        $this->_sTitle = t('Resend activation email');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->output();
    }

    public function soon()
    {
        // If the "member_remember" and "member_id" cookies do not exist, nothing happens.
        (new Framework\Cookie\Cookie)->remove( array('member_remember', 'member_id' ) );

        $this->_sTitle = t('See you soon!');
        $this->view->page_title = $this->_sTitle;
        $this->view->h2_title = $this->_sTitle;
        $this->design->setRedirect($this->registry->site_url, null, null, 3);
        $this->output();
    }

    public function logout()
    {
        (new User)->logout();
    }

    /**
     * Get the guest homepage template file.
     *
     * @return string The template filename.
     */
    private function _getGuestTplPage()
    {
        /**
         * When you are in the development mode, you can force the guest page by set a "force" GET request with the "splash" or "classic" parameter.
         * Example: "/?force=splash" or "/?force=classic"
         */
        if (isDebug() && $this->httpRequest->getExists('force'))
        {
            switch ($this->httpRequest->get('force'))
            {
                case 'classic':
                    $sPage = static::GUEST_FILE;
                break;

                case 'splash':
                    $sPage = static::GUEST_SPLASH_FILE;
                break;

                default:
                    exit('You can only choose between "classic" or "splash"');
            }
        }
        elseif ((!empty($this->_bIsMobApp) && $this->_bIsMobApp) || $this->browser->isMobile())
        {
            /* 'index.guest.inc.tpl' is not responsive enough for very small screen resolutions, so set to 'index.guest_splash.inc.tpl' by default */
            $sPage = static::GUEST_SPLASH_FILE;
        }
        else
        {
            $bIsSplashPage = (bool) DbConfig::getSetting('splashPage');
            $sPage = ($bIsSplashPage) ? static::GUEST_SPLASH_FILE : static::GUEST_FILE;
        }

        return $sPage;
    }

}
