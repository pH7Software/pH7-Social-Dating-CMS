<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Inc / Class
 */
namespace PH7;

use
PH7\Framework\Mail\Mail,
PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl,
PH7\Framework\Date\CDateTime,
PH7\Framework\Mvc\Model\DbConfig;

class Report
{
    private $_oView, $_mStatus = false;

    /**
     * Initialization of method(s)
     */
    public function __construct()
    {
        $this->_oView = new PH7Tpl;
    }

    /**
     * Add the fields in the database
     *
     * @param array $aData The data to  add
     * @return object this
     */
    public function add(array $aData)
    {
        $this->_mStatus = (new ReportModel)->add($aData);

        if ($this->_mStatus === true) {
            if (DbConfig::getSetting('sendReportMail')) {
                $this->sendMail($aData);
            }
        }

        return $this;
    }

    /**
     * Get status
     *
     * @return mixed (string | boolean) Text of the statute or boolean
     */
    public function get()
    {
        return $this->_mStatus;
    }

    /**
     * @param array $aData Report's details.
     * @return integer Number of recipients who were accepted for delivery.
     */
    protected function sendMail(array $aData)
    {
        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $sReporterUsername = $oUserModel->getUsername($aData['reporter_id']);
        $sSpammerUsername = $oUserModel->getUsername($aData['spammer_id']);
        $sDate = (new CDateTime)->get($aData['date'])->dateTime();

        $this->_oView->content =
        t('Reporter:') . ' <b><a href="' . $oUser->getProfileLink($sReporterUsername) . '">' . $sReporterUsername . '</a></b><br /><br /> ' .
        t('Spammer:') . ' <b><a href="' . $oUser->getProfileLink($sSpammerUsername) . '">' . $sSpammerUsername . '</a></b><br /><br /> ' .
        t('Contant Type:') . ' <b>' . $aData['type'] . '</b><br /><br /> ' .
        t('URL:') . ' <b>' . $aData['url'] . '</b><br /><br /> ' .
        t('Description of report:') . ' <b>' . $aData['desc'] . '</b><br /><br /> '.
        t('Date:') . ' <b>' . $sDate . '</b><br /><br />';

        unset($oUser, $oUserModel);

        $sHtmlMessage = $this->_oView->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/report/abuse.tpl', DbConfig::getSetting('adminEmail'));

        $aInfo = [
           'subject' => t('Spam report from %site_name%')
        ];

        return (new Mail)->send($aInfo, $sHtmlMessage);
    }
}
