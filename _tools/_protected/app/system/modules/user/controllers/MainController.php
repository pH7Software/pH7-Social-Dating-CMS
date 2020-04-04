<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Error\CException\PH7InvalidArgumentException;
use PH7\Framework\Mobile\MobApp;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class MainController extends Controller
{
    const GUEST_CLASSIC_PAGE_TYPE = 'classic';
    const GUEST_SPLASH_PAGE_TYPE = 'splash';

    const GUEST_SPLASH_FILE = 'index.guest_splash';
    const GUEST_FILE = 'index.guest';

    const REDIRECTION_DELAY_IN_SEC = 3;

    /** @var string */
    private $sTitle;

    /** @var bool */
    private $bIsMobile;

    /**
     * Displaying the main homepage of the website.
     */
    public function index()
    {
        /**
         * @internal We don't have to put the title here as it's the homepage, so it's the default title that is used.
         */

        // Used for profiles carousel/user block promo
        $this->view->userDesignModel = new UserDesignCoreModel;

        // For user counter
        $this->view->userDesign = new UserDesignCore;

        // Only visitors
        if (!UserCore::auth()) {
            // To check if the site is called by a Mobile or Mobile Native App
            $this->bIsMobile = $this->view->is_mobile = (MobApp::is($this->httpRequest, $this->session) || $this->browser->isMobile());

            $this->view->is_users_block = (bool)DbConfig::getSetting('usersBlock');

            $bIsBgVideo = false;
            if ($this->isGuestSplashPage()) {
                /**
                 * Background video is only available on the guest splash page.
                 */
                // Enable the Splash Video Background if enabled
                $bIsBgVideo = (bool)DbConfig::getSetting('bgSplashVideo');

                // Assign the background video option (this tpl var is only available in index.guest_splash.tpl)
                $this->view->is_bg_video = $bIsBgVideo;

                // Number of profiles to display on the profiles block
                $this->view->number_profiles = DbConfig::getSetting('numberProfileSplashPage');
            }

            $this->addGuestAssetFiles($bIsBgVideo);

            // Assigns the promo text to the view
            $this->view->promo_text = DbConfig::getMetaMain(PH7_LANG_NAME)->promoText;

            $this->manualTplInclude($this->getGuestTplPage() . '.inc.tpl');
        } else {
            $this->addUserAssetFiles();

            // Assigns the user's first name to the view for the Welcome Message
            $this->view->first_name = $this->session->get('member_first_name');

            $this->manualTplInclude('index.user.inc.tpl');
        }

        $this->output();
    }

    public function login()
    {
        // Display Sign In page
        $this->sTitle = t('Sign In to %site_name%');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function resendActivation()
    {
        // Display Resend Activation page
        $this->sTitle = t('Resend activation email');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function soon()
    {
        $this->sTitle = t('See you soon!');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->design->setRedirect(
            $this->registry->site_url,
            null,
            null,
            self::REDIRECTION_DELAY_IN_SEC
        );

        $this->output();
    }

    public function accountDeleted()
    {
        $this->sTitle = t('Sad to see you leaving us! :(');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->design->setRedirect(
            $this->registry->site_url,
            null,
            null,
            self::REDIRECTION_DELAY_IN_SEC
        );

        $this->output();
    }

    public function logout()
    {
        (new User)->logout($this->session);

        $this->redirectToSoonPage();
    }

    private function redirectToSoonPage()
    {
        Header::redirect(
            Uri::get('user', 'main', 'soon'),
            t('You are now logged out. Hope to see you again very soon!')
        );
    }

    /**
     * Get the guest homepage template file.
     *
     * @return string The template filename.
     *
     * @throws PH7InvalidArgumentException
     */
    private function getGuestTplPage()
    {
        if (isDebug() && $this->httpRequest->getExists('force')) {
            $sPage = $this->getGuestForcedPage();
        } elseif ($this->bIsMobile) {
            /* 'index.guest.inc.tpl' is not responsive enough for very small screen resolutions, so set to 'index.guest_splash.inc.tpl' by default */
            $sPage = static::GUEST_SPLASH_FILE;
        } else {
            $bIsSplashPage = (bool)DbConfig::getSetting('splashPage');
            $sPage = $bIsSplashPage ? static::GUEST_SPLASH_FILE : static::GUEST_FILE;
        }

        return $sPage;
    }

    /**
     * Add CSS/JS files for visitor's homepage.
     *
     * @param bool $bIsBgVideo
     *
     * @return void
     */
    private function addGuestAssetFiles($bIsBgVideo)
    {
        $sIsCssVidSplashFile = $bIsBgVideo === true ? 'video_splash.css,' : '';
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            $sIsCssVidSplashFile . 'splash.css,tooltip.css,js/jquery/carousel.css'
        );

        $this->design->addJs(
            PH7_DOT,
            PH7_STATIC . PH7_JS . 'jquery/carouFredSel.js,' . PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS . 'splash.js'
        );
    }

    /**
     * Add CSS/JS files for logged in users' homepage.
     *
     * @return void
     */
    private function addUserAssetFiles()
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS,
            'zoomer.css'
        );
        $this->design->addJs(
            PH7_STATIC . PH7_JS,
            'Wall.js'
        );
    }

    /**
     * When you are in the development mode, you can force the guest page by set a "force" GET request with the "splash" or "classic" parameter.
     * Example: "/?force=splash" or "/?force=classic"
     *
     * @return string
     *
     * @throws PH7InvalidArgumentException
     */
    private function getGuestForcedPage()
    {
        switch ($this->httpRequest->get('force')) {
            case self::GUEST_CLASSIC_PAGE_TYPE:
                $sPage = static::GUEST_FILE;
                break;

            case self::GUEST_SPLASH_PAGE_TYPE:
                $sPage = static::GUEST_SPLASH_FILE;
                break;

            default:
                throw new PH7InvalidArgumentException('You can only choose between "classic" or "splash"');
        }

        return $sPage;
    }

    /**
     * Returns TRUE if it's the guest splash page, FALSE otherwise.
     *
     * @return bool
     */
    private function isGuestSplashPage()
    {
        return $this->getGuestTplPage() === static::GUEST_SPLASH_FILE;
    }
}
