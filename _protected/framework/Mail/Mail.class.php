<?php
/**
 * @title            Mail Class
 * @desc             Mail Class derived from Swift Class
 *
 * @author           Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright        (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license          GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package          PH7 / Framework / Mail
 * @version          1.1 (Last update 01/14/2013)
 */

namespace PH7\Framework\Mail;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Model\DbConfig;

class Mail
{

    /**
     * Send an email with Swift library engine.
     *
     * @param array $aInfo
     * @param string $sContents
     * @param boolean $bHtmlFormat Default TRUE
     * @return integer Number of recipients who were accepted for delivery.
     */
    public function send(array $aInfo, $sContents, $bHtmlFormat = true)
    {
        // Default values
        $sFromMail = (empty($aInfo['from'])) ? DbConfig::getSetting('returnEmail') : $aInfo['from']; // Email noreply (generally noreply@yoursite.com)
        $sFromName = (empty($aInfo['form_name'])) ? DbConfig::getSetting('emailName') : $aInfo['form_name'];

        $sToMail = (empty($aInfo['to'])) ? DbConfig::getSetting('adminEmail') : $aInfo['to'];
        $sToName = (empty($aInfo['to_name'])) ? $sToMail : $aInfo['to_name'];

        $sSubject = $aInfo['subject'];

        // Setup the mailer
        $oTransport = \Swift_MailTransport::newInstance();
        $oMailer = \Swift_Mailer::newInstance($oTransport);
        $oMessage = \Swift_Message::newInstance()
            ->setSubject(escape($sSubject, true))
            ->setFrom(array(escape($sFromMail, true) => escape($sFromName, true)))
            ->setTo(array(escape($sToMail, true) => escape($sToName, true)));
        ($bHtmlFormat) ? $oMessage->addPart($sContents, 'text/html') : $oMessage->setBody($sContents);

        $iResult = $oMailer->send($oMessage);

        unset($oTransport, $oMailer, $oMessage);

        return $iResult;
    }

}
