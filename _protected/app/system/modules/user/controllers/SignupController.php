<?php
/**
 * @title          SignUp Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / User / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class SignupController extends Controller
{
    const TOTAL_SIGNUP_STEPS = 3;

    public function step1()
    {
        // Add CSS and JavaScript files for the left profiles block
        $this->design->addCss(PH7_LAYOUT . PH7_TPL . PH7_TPL_NAME . PH7_SH . PH7_CSS, 'zoomer.css');

        /*** Display Sign Up page ***/

        $bRef = $this->httpRequest->getExists('ref');
        $bUserRef = $this->httpRequest->getExists(['ref', 'a', 'u', 'f_n', 's']);

        if ($bRef || $bUserRef) {
            $sRef = $this->httpRequest->get('ref'); // For the statistics
            $sRefTxt = t('Reference: %0%', $sRef);
        }

        if ($bUserRef) {
            $sAction = $this->httpRequest->get('a'); // For the statistics
            $sUsername = $this->httpRequest->get('u'); // For the statistics and user image block
            $sSex = $this->httpRequest->get('s'); // For the statistics and user image block

            $sSessContents = $sRefTxt . ' | ' . t('Action: %0%', $sAction) . ' | ' . t('Sex: %0%', $sSex) . ' | ' . t('Username: %0%', $sUsername);
            $this->session->set(Registration::REFERENCE_VAR_NAME, $sSessContents);
        } elseif ($bRef) {
            $this->session->set(Registration::REFERENCE_VAR_NAME, $sRefTxt);
        }

        if ($bUserRef) {
            /* Enable the user image block in the view */
            $this->view->user_ref = 1;

            $sFirstName = str_replace('-', ' ', $this->str->upperFirst($this->httpRequest->get('f_n'))); // For user image block
            $this->view->username = $sUsername;
            $this->view->first_name = $sFirstName;
            $this->view->sex = $sSex;
        } else {
            /* For Members Block */
            $this->view->userDesignModel = new UserDesignCoreModel();
        }

        $this->view->page_title = ($bUserRef) ? t('Register for free to meet %0% on %site_name%. The Real Social Dating app!', $sFirstName) : t('Free Sign Up to Meet Lovely People!');

        if ($bUserRef) {
            $sH1Txt = t('Register for Free to Meet <span class="pink2">%0%</span> (<span class="pink1">%1%</span>) on <span class="pink2">%site_name%</span>!', $sFirstName, $this->str->upperFirst($sUsername));
        } else {
            $sH1Txt = t('Sign Up on %site_name%!');
        }

        $this->view->h1_title = '<div class="animated fadeInDown">' . $sH1Txt . '</div>';
        $this->view->meta_description = t('Sign Up today to meet friends, sex friends, singles, families, neighbors and many others people near or far from you! %site_name% is a free social dating with profiles, blogs, rating, hot or not, video chat rooms');

        $this->setupProgressbar(1, 33);

        $this->output();
    }

    public function step2()
    {
        $this->setTitle(t('Sign up - Step 2/3'));
        $this->setupProgressbar(2, 66);

        $this->output();
    }

    public function step3()
    {
        $this->setTitle(t('Sign up - Step 3/3'));
        $this->setupProgressbar(3, 99);

        $this->output();
    }

    public function step4()
    {
        $this->setTitle(t('Now, Upload a Profile Photo of you!'));
        $this->view->avatarDesign = new AvatarDesignCore; // Add AvatarDesign Class for displaying the avatar lightBox

        $this->output();
    }

    public function done()
    {
        if (!$this->session->exists('mail_step3')) {
            Header::redirect(Uri::get('user', 'signup', 'step3'));
        }

        // Remove all sessions created during registration
        $this->session->destroy();

        Header::redirect(
            Uri::get(
                'user',
                'main',
                'login'
            ),
            (new Registration($this->view))->getMsg()
        );
    }

    /**
     * @param int $iStep Number of the current step (e.g. 1, 2, 3).
     * @param int $iPercentage Percentage of progression.
     *
     * @return void
     */
    protected function setupProgressbar($iStep, $iPercentage)
    {
        $this->view->progressbar_percentage = $iPercentage;
        $this->view->progressbar_step = $iStep;
        $this->view->progressbar_total_steps = self::TOTAL_SIGNUP_STEPS;
    }

    /**
     * Set title and heading.
     *
     * @param string $sTitle
     *
     * @return void
     */
    private function setTitle($sTitle)
    {
        $this->view->page_title = $this->view->h1_title = $sTitle;
    }
}
