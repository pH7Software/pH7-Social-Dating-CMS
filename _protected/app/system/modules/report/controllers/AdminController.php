<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Report / Controller
 */

declare(strict_types=1);

namespace PH7;

use PH7\Datatype\Type;
use PH7\Framework\Layout\Html\Security;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Navigation\Page;
use PH7\Framework\Security\CSRF\Token;
use PH7\Framework\Url\Header;

class AdminController extends Controller
{
    use BulkAction;

    private const REPORTS_PER_PAGE = 15;

    private ReportModel $oReportModel;

    private string $sTitle;

    private string $sMsg;

    private bool $bStatus;

    public function __construct()
    {
        parent::__construct();

        $this->oReportModel = new ReportModel;

        $this->view->avatarDesign = new AvatarDesignCore; // Avatar Design Class
        $this->view->oUserModel = new UserCoreModel;
    }

    public function index(): void
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
    public function report($iId = 0): void
    {
        $iId = (int)$iId;

        $this->sTitle = t('Report #%0%', $iId);
        $this->view->page_title = $this->sTitle;
        $this->view->h1_title = $this->sTitle;
        $this->view->dateTime = $this->dateTime;
        $this->view->report = $this->oReportModel->get($iId, 0, 1);

        $this->output();
    }

    public function delete(): void
    {
        $this->bStatus = $this->oReportModel->delete($this->httpRequest->post('id', Type::INTEGER));
        $this->sMsg = $this->bStatus ? t('The report has been deleted.') : t('A problem occurred during the deleted of the reporting.');

        Header::redirect(Uri::get('report', 'admin', 'index'), $this->sMsg);
    }

    public function deleteAll(): void
    {
        $aActions = $this->httpRequest->post('action');
        $bActionsEligible = $this->areActionsEligible($aActions);

        if (!(new Token)->check('report_action')) {
            $this->sMsg = Form::errorTokenMsg();
        } elseif ($bActionsEligible) {
            foreach ($aActions as $iId) {
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
