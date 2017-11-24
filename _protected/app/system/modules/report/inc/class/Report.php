<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Report / Inc / Class
 */

namespace PH7;

use PH7\Framework\Date\CDateTime;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;

class Report
{
    /** @var PH7Tpl */
    private $oView;

    /** @var string|bool */
    private $mStatus = false;

    /**
     * @param PH7Tpl $oView
     */
    public function __construct(PH7Tpl $oView)
    {
        $this->oView = $oView;
    }

    /**
     * Add the fields in the database.
     *
     * @param array $aData The data to  add
     *
     * @return Report
     */
    public function add(array $aData)
    {
        $oExistsModel = new ExistsCoreModel;

        if ($oExistsModel->id($aData['reporter_id']) && $oExistsModel->id($aData['spammer_id'])) {
            $this->mStatus = (new ReportModel)->add($aData);

            if ($this->mStatus === true) {
                if (DbConfig::getSetting('sendReportMail')) {
                    $this->sendMail($aData);
                }
            }
        }

        return $this;
    }

    /**
     * Get status
     *
     * @return string|bool Text of the statute or boolean
     */
    public function get()
    {
        return $this->mStatus;
    }

    /**
     * @param array $aData Report's details.
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    protected function sendMail(array $aData)
    {
        $oUser = new UserCore;
        $oUserModel = new UserCoreModel;
        $sReporterUsername = $oUserModel->getUsername($aData['reporter_id']);
        $sSpammerUsername = $oUserModel->getUsername($aData['spammer_id']);
        $sDate = (new CDateTime)->get($aData['date'])->dateTime();

        $this->oView->content =
            t('Reporter:') . ' <b><a href="' . $oUser->getProfileLink($sReporterUsername) . '">' . $sReporterUsername . '</a></b><br /><br /> ' .
            t('Spammer:') . ' <b><a href="' . $oUser->getProfileLink($sSpammerUsername) . '">' . $sSpammerUsername . '</a></b><br /><br /> ' .
            t('Contant Type:') . ' <b>' . $aData['type'] . '</b><br /><br /> ' .
            t('URL:') . ' <b>' . $aData['url'] . '</b><br /><br /> ' .
            t('Description of report:') . ' <b>' . $aData['desc'] . '</b><br /><br /> ' .
            t('Date:') . ' <b>' . $sDate . '</b><br /><br />';

        unset($oUser, $oUserModel);

        $sHtmlMessage = $this->oView->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/report/abuse.tpl',
            DbConfig::getSetting('adminEmail')
        );

        $aInfo = [
            'subject' => t('Abuse report from %site_name%')
        ];

        return (new Mail)->send($aInfo, $sHtmlMessage);
    }
}
