<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Asset / Ajax
 */
namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http;

class ReportAjax
{
    private $_oHttpRequest, $_oReportModel, $_bStatus;

    public function __construct()
    {
        if (!(new Framework\Security\CSRF\Token)->check('report'))
        exit(jsonMsg(0, Form::errorTokenMsg()));

        $this->_oHttpRequest = new Http;
        $this->_oReportModel = new ReportModel;

        switch ($this->_oHttpRequest->post('type'))
        {
            case 'delete':
                $this->delete();
            break;

            default:
                Framework\Http\Http::setHeadersByCode(400);
                exit('Bad Request Error');
        }
    }

    protected function delete()
    {
        $this->_bStatus = $this->_oReportModel->delete($this->_oHttpRequest->post('reportId'));
        echo ($this->_bStatus) ? jsonMsg(1, t('The report has been deleted.')) : jsonMsg(0, t('Cannot remove the report. Please try later.'));
    }

    public function __destruct()
    {
        unset($this->_oHttpRequest, $this->_oReportModel, $this->_bStatus);
    }
}

// Only for Admins
if (AdminCore::auth())
    new ReportAjax;
