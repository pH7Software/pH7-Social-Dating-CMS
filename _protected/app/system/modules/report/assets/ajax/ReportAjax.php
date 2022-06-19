<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Report / Asset / Ajax
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Datatype\Type;
use PH7\Framework\Http\Http;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Security\CSRF\Token;
use PH7\JustHttp\StatusCode;

class ReportAjax
{
    private HttpRequest $oHttpRequest;

    private ReportModel $oReportModel;

    public function __construct()
    {
        if (!(new Token)->check('report')) {
            exit(jsonMsg(0, Form::errorTokenMsg()));
        }

        $this->oHttpRequest = new HttpRequest;
        $this->oReportModel = new ReportModel;

        switch ($this->oHttpRequest->post('type')) {
            case 'delete':
                echo $this->delete();
                break;

            default:
                echo $this->badRequest();
                exit;
        }
    }

    /**
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function delete(): string
    {
        $bStatus = $this->oReportModel->delete($this->oHttpRequest->post('reportId', Type::INTEGER));

        if ($bStatus) {
            return jsonMsg(1, t('The report has been deleted.'));
        }

        return jsonMsg(0, t('Cannot remove the report. Please try later.'));
    }

    /**
     * @throws Framework\Http\Exception
     */
    private function badRequest(): string
    {
        Http::setHeadersByCode(StatusCode::BAD_REQUEST);

        return 'Bad Request Error';
    }
}

// Only for Admins
if (AdminCore::auth()) {
    new ReportAjax;
}
