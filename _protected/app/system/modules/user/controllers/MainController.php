<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;

class MainController extends Controller
{
    private $sTitle;

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
            // Set CSS and JS files
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'splash.css,tooltip.css,js/jquery/carousel.css');
            $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'jquery/carouFredSel.js,' . PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_JS . 'splash.js');

            // Assigns the promo text to the view
            $this->view->promo_text = DbConfig::getMetaMain(PH7_LANG_NAME)->promoText;

            // Assign the background video option
            $this->view->is_bg_video = DbConfig::getSetting('bgSplashVideo');

            /**
             * When you are in the development mode, you can force the guest page by set a "force" GET request with the "splash" or "classic" parameter.
             * Example: "/?force=splash" or "/?force=classic"
             */
             if (isDebug() && $this->httpRequest->getExists('force'))
             {
                 switch ($this->httpRequest->get('force'))
                 {
                     case 'classic':
                         $sPage = 'index.guest';
                     break;

                     case 'splash':
                         $sPage = 'index.guest_splash';
                     break;

                     default:
                         exit('You can only choose between "classic" or "splash"');
                }
            }
            else
            {
                $bIsSplashPage = (bool) DbConfig::getSetting('splashPage');
                $sPage = ($bIsSplashPage) ? 'index.guest_splash' : 'index.guest';
            }
            $this->manualTplInclude($sPage . '.inc.tpl');
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
        $this->sTitle = t('Sign In to %site_name%!');
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
        // If the "member_remember" and "member_id" cookies do not exist, nothing happens.
        (new Framework\Cookie\Cookie)->remove( array('member_remember', 'member_id' ) );

        $this->sTitle = t('See you soon!');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->design->setRedirect($this->registry->site_url, null, null, 3);
        $this->output();
    }

    public function logout()
    {
        (new User)->logout();
    }

}
