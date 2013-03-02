<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */
namespace PH7;

class MainController extends Controller
{
    private $sTitle;

    public function index()
    {
        // Display home page of web site
        // We must not put the title as this is the home page, so this is the default title is used.

        /**** BEGIN Style sheet and JS files ****/
        // Only visitors
        if (!UserCore::auth()) {
            $this->view->is_splash_page = Framework\Mvc\Model\DbConfig::getSetting('splashPage');

            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_CSS, 'splash.css,tooltip.css,js/jquery/carousel.css');
            $this->design->addJs(PH7_DOT, PH7_STATIC . PH7_JS . 'jquery/carouFredSel.js,' . PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_JS . 'splash.js');
        }

        // Only Members
        if (UserCore::auth()) {
            $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_DS . PH7_CSS, 'zoomer.css');
            $this->design->addJs(PH7_STATIC . PH7_JS, 'zoomer.js,Wall.js');
            $this->view->first_name = $this->session->get('member_first_name'); // First Name for the welcome message.
        }

        /**** END Style sheet and JS files ****/

        // For Profiles Carousel
        $this->view->userDesignModel = new UserDesignCoreModel;
        $this->view->userDesign = new UserDesignCore;
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
