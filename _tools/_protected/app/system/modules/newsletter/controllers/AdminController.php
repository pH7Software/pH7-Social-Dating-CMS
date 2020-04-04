<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token as SecurityToken;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    const SUBSCRIBERS_PER_PAGE = 30;
    const REDIRECTION_DELAY_IN_SEC = 5;

    /** @var SubscriberModel */
    private $oSubscriberModel;

    /** @var string */
    private $sTitle;

    public function __construct()
    {
        parent::__construct();

        $this->oSubscriberModel = new SubscriberModel;
    }

    public function index()
    {
        $this->sTitle = t('Newsletter');
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;

        $this->output();
    }

    public function search()
    {
        $this->sTitle = t('Search Subscribers');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $this->output();
    }

    public function browse()
    {
        $sKeywords = $this->httpRequest->get('looking');
        $sOrder = $this->httpRequest->get('order');
        $iSort = $this->httpRequest->get('sort', 'int');

        $iTotal = $this->oSubscriberModel->browse(
            $sKeywords,
            true,
            $sOrder,
            $iSort,
            null,
            null
        );

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($iTotal, self::SUBSCRIBERS_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        $oBrowse = $this->oSubscriberModel->browse(
            $sKeywords,
            false,
            $sOrder,
            $iSort,
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage()
        );
        unset($oPage);

        if (empty($oBrowse)) {
            $this->setNotFoundPage();
        } else {
            // Add the js file for the browse form
            $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

            // Assigns variables for views
            $this->view->designSecurity = new Framework\Layout\Html\Security; // Security Design Class
            $this->view->dateTime = $this->dateTime; // Date Time Class

            $this->sTitle = t('Browse Subscribers');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->h3_title = nt('%n% subscriber found', '%n% subscribers found', $iTotal);

            $this->view->browse = $oBrowse;
        }

        $this->output();
    }

    public function deleteAll()
    {
        $sMsg = ''; // Default msg value

        if (!(new SecurityToken)->check('subscriber_action')) {
            $sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sEmail) {
                $this->oSubscriberModel->unsubscribe($sEmail);
            }

            $sMsg = t('The subscribers(s) has/have been removed.');
        }

        Header::redirect(
            Uri::get('newsletter', 'admin', 'browse'),
            $sMsg
        );
    }

    /**
     * Redirects to admin browse page, then displays the default "Not Found" page.
     *
     * @return void
     */
    private function setNotFoundPage()
    {
        $this->design->setRedirect(
            Uri::get(
                'newsletter',
                'admin',
                'browse'
            ),
            null,
            null,
            self::REDIRECTION_DELAY_IN_SEC
        );
        $this->displayPageNotFound(t('Sorry, Your search returned no results!'));
    }
}
