<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Admin / Controller
 */

namespace PH7;

use PH7\Framework\Core\Kernel;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Security\Version;
use PH7\Framework\Url\Header;
use PH7\Framework\Url\Url;

class InfoController extends Controller
{
    const TWITTER_TWEET_URL = 'https://twitter.com/intent/tweet?text=';

    /** @var string */
    private $sTitle;

    public function index()
    {
        Header::redirect(Uri::get(PH7_ADMIN_MOD, 'info', 'software'));
    }

    public function language()
    {
        $this->sTitle = t('PHP Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function software()
    {
        $this->sTitle = t('%software_name% Information');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->release_date = $this->dateTime->get(Version::KERNEL_RELASE_DATE)->date();
        $this->view->license_form_link = Uri::get(PH7_ADMIN_MOD, 'setting', 'license');
        $this->view->tweet_msg = $this->getTweetPost();

        $this->output();
    }

    private function getTweetPost()
    {
        $sMsg = t('I built my social dating business with pH7CMS -> %0%', Kernel::SOFTWARE_WEBSITE);

        return self::TWITTER_TWEET_URL . Url::encode($sMsg);
    }
}
