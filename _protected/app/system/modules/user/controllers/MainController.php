<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mobile\MobApp;
use PH7\Framework\Mvc\Model\DbConfig;

class MainController extends Controller
{
    const GUEST_SPLASH_FILE = 'index.guest_splash';
    const GUEST_FILE = 'index.guest';

    /** @var string */
    private $_sTitle;

    /** @var bool */
    private $_bIsMobile;

    /**
     * Displaying the main homepage of the website.
     */
    public function index()
    {
        // We don't have to put the title here as it's the homepage, so it's the default title that is used.

        // For Profiles Carousel
        $this->view->userDesignModel = new UserDesignCoreModel;

        // For user counter
        $this->view->userDesign = new UserDesignCore;

        // Only visitors
        if (!UserCore::auth()) {
            // To check if the site is called by a Mobile or Mobile Native App
            $this->_bIsMobile = $this->view->is_mobile = (MobApp::is($this->httpRequest, $this->session) || $this->browser->isMobile());

            $this->view->is_users_block = (bool) DbConfig::getSetting('usersBlock');

            // Background video is used only for the Splash page
            if ($this->_getGuestTplPage() === static::GUEST_SPLASH_FILE) {
                // Enable the Splash Video Background if enabled
                $bIsBgVideo = (bool) DbConfig::getSetting('bgSplashVideo');

                // Assign the background video option (this tpl var is only available in index.guest_splash.tpl)
                $this->view->is_bg_video = $bIsBgVideo;

                // Number of profiles to display on the profiles block
                $this->view->number_profiles = DbConfig::getSetting('numberProfileSplashPage');
            }

            $sIsCssVidSplashFile = (!empty($bIsBgVideo) && $bIsBgVideo) ? 'video_splash.css,' : '';

            // Set CSS and JS files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, $sIsCssVidSplashFile . 'splash.css,tooltip.css,js/jquery/carousel.css');
            $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'jquery/carouFredSel.js,' . PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS . 'splash.js');

            // Assigns the promo text to the view
            $this->view->promo_text = DbConfig::getMetaMain(PH7_LANG_NAME)->promoText;

            $this->manualTplInclude($this->_getGuestTplPage() . '.inc.tpl');
        } elseif (UserCore::auth()) {
            // Set CSS and JS files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'zoomer.css');
            $this->design->addJs(PH7_STATIC . PH7_JS, 'Wall.js');

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
        if (isDebug() && $this->httpRequest->getExists('force')) {
            $sPage = $this->_getPageForced();
        } elseif ($this->_bIsMobile || $this->browser->isMobile()) {
            /* 'index.guest.inc.tpl' is not responsive enough for very small screen resolutions, so set to 'index.guest_splash.inc.tpl' by default */
            $sPage = static::GUEST_SPLASH_FILE;
        } else {
            $bIsSplashPage = (bool) DbConfig::getSetting('splashPage');
            $sPage = ($bIsSplashPage) ? static::GUEST_SPLASH_FILE : static::GUEST_FILE;
        }

        return $sPage;
    }

    /**
     * When you are in the development mode, you can force the guest page by set a "force" GET request with the "splash" or "classic" parameter.
     * Example: "/?force=splash" or "/?force=classic"
     *
     * @throws PH7InvalidArgumentException
     *
     * @return string
     */
    private function _getPageForced()
    {
        switch ($this->httpRequest->get('force')) {
            case 'classic':
                $sPage = static::GUEST_FILE;
                break;

            case 'splash':
                $sPage = static::GUEST_SPLASH_FILE;
                break;

            default:
                throw new PH7InvalidArgumentException('You can only choose between "classic" or "splash"');
        }

        return $sPage;
    }
}
