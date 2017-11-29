<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Controller
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    const SUBSCRIBERS_PER_PAGE = 30;

    /** @var SubscriptionModel */
    private $oSubscriptionModel;

    /** @var string */
    private $sTitle;

    public function __construct()
    {
        parent::__construct();

        $this->oSubscriptionModel = new SubscriptionModel;
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
        $oSubscriptionModel = new SubscriptionModel;

        $iTotal = $this->oSubscriptionModel->browse(
            $this->httpRequest->get('looking'),
            true,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            null,
            null
        );

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages($iTotal, self::SUBSCRIBERS_PER_PAGE);
        $this->view->current_page = $oPage->getCurrentPage();
        $oBrowse = $this->oSubscriptionModel->browse(
            $this->httpRequest->get('looking'),
            false,
            $this->httpRequest->get('order'),
            $this->httpRequest->get('sort'),
            $oPage->getFirstItem(),
            $oPage->getNbItemsPerPage()
        );
        unset($oPage);

        if (empty($oBrowse)) {
            $this->design->setRedirect(Uri::get('newsletter', 'admin', 'browse'));
            $this->displayPageNotFound(t('Sorry, Your search returned no results!'));
        } else {
            // Add the js file necessary for the browse form
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
        if (!(new Framework\Security\CSRF\Token)->check('subscriber_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $sEmail) {
                $this->oSubscriptionModel->unsubscribe($sEmail);
            }

            $this->sMsg = t('The subscribers(s) has/have been removed.');
        }

        Header::redirect(
            Uri::get('newsletter', 'admin', 'browse'),
            $this->sMsg
        );
    }
}
