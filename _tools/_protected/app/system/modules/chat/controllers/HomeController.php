<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Chat / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Parse\SysVar;
use PH7\Framework\Url\Url;

class HomeController extends Controller
{
    public function index()
    {
        $this->view->page_title = t('Free Chat Room Dating');
        $this->view->meta_description = t('Find Your Match at The Best Free Online Dating Site with Free Chat Rooms, Single Chat Meet People');
        $this->view->meta_keywords = t('chat, speed dating, meet singles, dating, free dating, chat room, chat webcam');
        $this->view->h1_title = t('Welcome to <span class="pH3">Free Chat Room</span> on <span class="pH0">%site_name%</span>!');
        $this->view->chat_room = Url::clean((new SysVar)->parse(DbConfig::getSetting('chatApi')));

        $this->output();
    }
}
