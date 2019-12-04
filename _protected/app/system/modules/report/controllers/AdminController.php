<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Security;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    const REPORTS_PER_PAGE = 15;

    /** @var ReportModel */
    private $oReportModel;

    /** @var string */
    private $sTitle;

    /** @var string */
    private $sMsg;

    /** @var bool */
    private $bStatus;

    public function __construct()
    {
        parent::__construct();

        $this->oReportModel = new ReportModel;

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->oUserModel = new UserCoreModel;
    }

    public function index()
    {
        // CSRF Token
        $this->view->csrf_token = (new Token)->generate('report');

        // Security Design Class
        $this->view->designSecurity = new Security;

        // Adding the JS files for the report and form.
        $this->design->addJs(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_JS,
            'common.js'
        );
        $this->design->addJs(PH7_STATIC . PH7_JS, 'form.js');

        $iTotalReports = ReportModel::totalReports();

        $this->sTitle = nt('%n% Report', '%n% Reports', $iTotalReports);
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        $oPage = new Page;
        $this->view->total_pages = $oPage->getTotalPages(
            $iTotalReports,
            self::REPORTS_PER_PAGE
        );
        $this->view->current_page = $oPage->getCurrentPage();
        $this->view->reports = $this->oReportModel->get(null, $oPage->getFirstItem(), $oPage->getNbItemsPerPage());
        unset($oPage);

        $this->output();
    }

    /**
     * @param int $iId
     *
     * @return void
     */
    public function report($iId = 0)
    {
        $iId = (int)$iId;

        $this->sTitle = t('Report #%0%', $iId);
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->dateTime = $this->dateTime;
        $this->view->report = $this->oReportModel->get($iId, 0, 1);

        $this->output();
    }

    public function delete()
    {
        $this->bStatus = $this->oReportModel->delete($this->httpRequest->post('id', 'int'));
        $this->sMsg = $this->bStatus ? t('The report has been deleted.') : t('A problem occurred during the deleted of the reporting.');

        Header::redirect(Uri::get('report', 'admin', 'index'), $this->sMsg);
    }

    public function deleteAll()
    {
        if (!(new Token)->check('report_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif (count($this->httpRequest->post('action')) > 0) {
            foreach ($this->httpRequest->post('action') as $iId) {
                $iId = (int)$iId;
                $this->oReportModel->delete($iId);
            }
            $this->sMsg = t('Report successfully deleted.');
        }

        Header::redirect(
            Uri::get('report', 'admin', 'index'),
            $this->sMsg
        );
    }
}
