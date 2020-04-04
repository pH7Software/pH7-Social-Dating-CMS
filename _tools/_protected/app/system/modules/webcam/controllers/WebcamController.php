<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Webcam / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Url\Header;

class WebcamController extends Controller
{
    /** @var string */
    private $sTitle;

    public function index()
    {
        Header::redirect(Uri::get('webcam', 'webcam', 'picture'));
    }

    public function picture()
    {
        $this->sTitle = t('Webcam Picture Party Fun');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->h2_title = t('Come take your best pictures of you and your friends on the wall of the best photo shoots!');
        $this->view->h3_title = t('Guaranteed fun!');

        // Add Css Style and JavaScript for the Webcam
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            'style.css'
        );
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'webcam.js,script.js'
        );

        $this->output();
    }

    public function video()
    {
        $this->sTitle = t('Webcam Video Party Fun');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->h2_title = t('Come take your best videos of you and your friends on the wall of the best video shoots!');
        $this->view->h3_title = t('Guaranteed fun!');

        /**
         * Video method is still under development, if you are a FLASH/PHP or HTML5/JS(WebRTC API)/PHP developer and you want to help us and join our volunteer team of developers to continue development of video capture, you're welcome!
         * Please use our open source Github repo: https://github.com/pH7Software/pH7-Social-Dating-CMS
         *
         * Thank you,
         * pH7 developers team (Pierre-Henry Soria).
         */
    }
}
