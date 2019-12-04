<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Chatroulette / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Parse\SysVar;
use PH7\Framework\Url\Url;

class HomeController extends Controller
{
    public function index()
    {
        $this->view->page_title = t('Free Video Room, Live Speed Dating ChatRoulette');
        $this->view->meta_description = t('Free Live Speed Dating with the Chatroulette of %site_name%, Meet new people near you and make new friends, sex friends and free flirting, Free online dating site for singles without registration with Video Chat Rooms!');
        $this->view->meta_keywords = t('chat, chatroulette, sex friend, single, speed dating, meet singles, dating, free dating, chat room, chat webcam');
        $this->view->h1_title = t('Welcome to <span class="pH1">Speed Dating ChatRoulette</span> of <span class="pH0">%site_name%</span>!');
        $this->view->chatroulette = Url::clean((new SysVar)->parse(DbConfig::getSetting('chatrouletteApi')));

        $this->output();
    }
}
