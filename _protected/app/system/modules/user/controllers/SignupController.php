<?php
/**
 * @title          SignUp Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 * @version        1.0
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri, PH7\Framework\Url\Header;

class SignupController extends Controller
{

    private $sTitle;

    public function step1()
    {
        // Add CSS and JavaScript files for the left profiles block
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'zoomer.css');
        //$this->design->addJs(PH7_STATIC . PH7_JS, 'zoomer.js');

        /*** Display Sign Up page ***/

        $bRef = $this->httpRequest->getExists('ref');
        $bUserRef = $this->httpRequest->getExists(array('ref', 'a', 'u', 'f_n', 's'));

        if ($bRef || $bUserRef)
        {
            $sRef = $this->httpRequest->get('ref'); // For the statistics
            $sRefTxt = t('Reference: %0%', $sRef);
        }

        if ($bUserRef)
        {
            $sAction = $this->httpRequest->get('a'); // For the statistics
            $sUsername = $this->httpRequest->get('u'); // For the statistics and user image block
            $sSex = $this->httpRequest->get('s'); // For the statistics and user image block

            $sSessContents = $sRefTxt . ' | ' . t('Action: %0%', $sAction) . ' | ' . t('Sex: %0%', $sSex) . ' | ' . t('Username: %0%', $sUsername);
            $this->session->set('join_ref', $sSessContents);
        }
        elseif ($bRef)
        {
            $this->session->set('join_ref', $sRefTxt);
        }

        if ($bUserRef)
        {
            /* Enable the user image block in the view */
            $this->view->user_ref = 1;

            $sFirstName = str_replace('-', ' ', $this->str->upperFirst($this->httpRequest->get('f_n'))); // For user image block
            $this->view->username = $sUsername;
            $this->view->first_name = $sFirstName;
            $this->view->sex = $sSex;
        }
        else
        {
            /* For Members Block */
            $this->view->userDesignModel = new UserDesignCoreModel();
        }

        $this->view->page_title = ($bUserRef) ? t('Register for free to meet %0% on %site_name%. The Real Social Dating app!',  $sFirstName) : t('Free Sign Up to Meet Lovely People!');

        if ($bUserRef) {
            $sH1Txt = t('Register for Free to Meet <span class="pink2">%0%</span> (<span class="pink1">%1%</span>) on <span class="pink2">%site_name%</span>!', $sFirstName, $this->str->upperFirst($sUsername));
        } else {
            $sH1Txt = t('Sign Up on %site_name%!');
        }

        $this->view->h1_title = '<div class="animated fadeInDown">' . $sH1Txt . '</div>';
        $this->view->meta_description = t('Sign Up today to meet friends, sex friends, singles, families, neighbors and many others people near or far from you! %site_name% is a free social dating with profiles, blogs, rating, hot or not, video chat rooms');

        $this->output();
    }

    public function step2()
    {
        $this->sTitle = t('Sign up - Step 2/3');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function step3()
    {
        $this->sTitle = t('Sign up - Step 3/3');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function step4()
    {
        $this->sTitle = t('Now, Upload a Profile Photo of you!');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->output();
    }

    public function done()
    {
        if (!$this->session->exists('mail_step3'))
            Header::redirect(Uri::get('user','signup','step3'));

        $this->session->destroy(); // Remove all sessions created pending registration
        Header::redirect(Uri::get('user','main','login'), (new Registration)->getMsg());
    }

}
