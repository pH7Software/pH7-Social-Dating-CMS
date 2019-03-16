<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Profile Faker / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\PayPal;
use PH7\Framework\Url\Header;

class GeneratorController extends Controller
{
    public function addMember()
    {
        $this->setTitle(t('Generate Fake Members'));
        $this->output();
    }

    public function addAffiliate()
    {
        $this->setTitle(t('Generate Fake Affiliates'));
        $this->output();
    }

    public function addSubscriber()
    {
        $this->setTitle(t('Generate Fake Subscribers'));
        $this->output();
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
