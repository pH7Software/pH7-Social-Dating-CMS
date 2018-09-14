<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Controller
 */

namespace PH7;

use PH7\Framework\Url\Url;

class MainController extends Controller
{
    const TWITTER_TWEET_URL = 'https://twitter.com/intent/tweet?text=';
    const TWITTER_TWEET_MSG = "#WOW! I'm the %0%th user on %site_url%! #milestone succeeded!!!";

    /** @var UserCoreModel */
    private $oUserModel;

    public function __construct()
    {
        $this->oUserModel = new UserCoreModel;
    }

    public function awesome()
    {
        $iTotalUsers = $this->oUserModel->total();
        $this->view->page_title = $this->view->h1_title = t('You are AWESOME!!! 🎉');

        $this->view->message = t('Wow! You are the %0%th! YOU ARE AWESOME! 😍', $iTotalUsers);
        $this->view->tweet_msg_url = $this->getTweetPost($iTotalUsers);

        $this->notifyAdmin();
        $this->output();
    }

    private function notifyAdmin()
    {
        (new MilestoneNotifier($this->oUserModel, new Mail, $this->view))
            ->sendEmailToAdmin();
    }

    /**
     * @param int $iTotalUsers
     *
     * @return string
     */
    private function getTweetPost($iTotalUsers)
    {
        $sMsg = t(self::TWITTER_TWEET_MSG, $iTotalUsers);

        return self::TWITTER_TWEET_URL . Url::encode($sMsg);
    }
}
