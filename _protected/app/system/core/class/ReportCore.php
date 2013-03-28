<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Core / Class
 */
namespace PH7;

use
PH7\Framework\Mail\Mail,
PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl,
PH7\Framework\Date\CDateTime,
PH7\Framework\Mvc\Model\DbConfig;

class ReportCore
{

    private $_oView, $_sUrl, $_sType, $_sDesc, $_sDate, $_iReporterId, $_iSpammerId, $_mStatus = false;

    /**
     * @desc Constructor Initialization of methods
     */
    public function __construct(array $aData)
    {
        $this->_oView = new PH7Tpl;

        $this->_iReporterId = $aData['reporter_id'];
        $this->_iSpammerId = $aData['spammer_id'];
        $this->_sUrl = $aData['url'];
        $this->_sType = $aData['type'];
        $this->_sDesc = $aData['desc'];
        $this->_sDate = $aData['date'];
    }

    /**
     * @desc Add the fields in the database
     * @return object this
     */
    public function add()
    {
        $aData = [
            'reporter_id' => $this->_iReporterId,
            'spammer_id' => $this->_iSpammerId,
            'url' => $this->_sUrl,
            'type' => $this->_sType,
            'desc' => $this->_sDesc,
            'date' => $this->_sDate
        ];

        $this->_mStatus = (new ReportCoreModel)->add($aData);

        unset($aData);

        if ($this->_mStatus == true)
        {
            if (DbConfig::getSetting('sendReportMail'))
            {
                $oUserModel = new UserCoreModel;
                $sDate = (new CDateTime)->get($this->_sDate)->dateTime();

                $this->_oView->content =
                t('Reporter:') . ' <b><a href="' . PH7_URL_ROOT . $oUserModel->getUsername($this->_iReporterId) . PH7_PAGE_EXT . '">' . $oUserModel->getUsername($this->_iReporterId) . '</a></b><br /><br /> ' .
                t('Spammer:') . ' <b><a href="' . PH7_URL_ROOT . $oUserModel->getUsername($this->_iSpammerId) . PH7_PAGE_EXT . '">' . $oUserModel->getUsername($this->_iSpammerId) . '</a></b><br /><br /> ' .
                t('Contant Type:') . ' <b>' . $this->_sType . '</b><br /><br /> ' .
                t('URL:') . ' <b>' . $this->_sUrl . '</b><br /><br /> ' .
                t('Description of report:') . ' <b>' . $this->_sDesc . '</b><br /><br /> '.
                t('Date:') . ' <b>' . $sDate . '</b><br /><br />';

                unset($oUserModel, $sDate);

                $sMessageHtml = $this->_oView->parseMail(PH7_PATH_SYS . 'globals/' . PH7_VIEWS . PH7_TPL_NAME . '/mails/sys/mod/report/abuse.tpl', DbConfig::getSetting('adminEmail'));

                $aInfo = [
                   'subject' => t('Spam report from %site_name%')
                ];

                (new Mail)->send($aInfo, $sMessageHtml);
            }
        }

        return $this;
    }

    /**
     * @desc Get status
     * @return mixed (string | boolean) Text of the statute or boolean
     */
    public function get()
    {
        return $this->_mStatus;
    }

}
