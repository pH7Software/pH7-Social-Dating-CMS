<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Milestone Celebration / Controller
 */

namespace PH7;

class MainController extends Controller
{
    /** @var UserCoreModel */
    private $oUserModel;

    public function __construct()
    {
        $this->oUserModel = new UserCoreModel;
    }

    public function awesome()
    {
        $this->view->page_title = $this->view->h1_title = t('You are AWESOME!!! 🎉');

        $this->view->message = t('Wow! You are the %0%th! YOU ARE AWESOME! 😍', $this->oUserModel->total());
        $this->notifyAdmin();
        $this->output();
    }

    private function notifyAdmin()
    {
        (new MilestoneNotifier($this->oUserModel, new Mail, $this->view))
            ->sendEmailToAdmin();
    }
}
