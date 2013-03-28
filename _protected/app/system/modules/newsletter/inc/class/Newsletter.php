<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Inc / Class
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\HttpRequest, PH7\Framework\Mail\Mail;

class Newsletter extends Core
{

    private $_oSubscriptionModel;
    private static $_iTotalSent = 0;

    public function __construct()
    {
        parent::__construct();

        $this->_oSubscriptionModel = new SubscriptionModel;

    }

    /**
     * Send the newsletter to subscribers.
     *
     * @return array (integer | integer) ['status', 'nb_mail_sent']
     */
    public function sendMessages()
    {
        $bOnlySubscribers = ($this->httpRequest->get('only_subscribers') == '1');
        $oMail = new Mail;
        $iRes = 0; // Default value

        if ($bOnlySubscribers)
            $oSubscribers = $this->_oSubscriptionModel->getSubscribers();
        else
            $oSubscribers = $this->_oSubscriptionModel->getProfiles();

        foreach ($oSubscribers as $oSubscriber)
        {
            // Do not send any emails at the same time to avoid overloading the mail server.
            if (self::$_iTotalSent > 250) sleep(10);

            $this->view->content = $this->httpRequest->post('body', HttpRequest::NO_CLEAN);

            $sMsgHtml = $this->view->parseMail(PH7_PATH_SYS . 'globals/' . PH7_VIEWS . PH7_TPL_NAME . '/mails/sys/mod/newsletter/msg.tpl', $oSubscribers->email);

            $aInfo = [
                'subject' => $this->httpRequest->post('subject'),
                'to' => $oSubscribers->email,
                'to_name' => ($bOnlySubscribers) ? $oSubscribers->name : $oSubscribers->firstName
            ];

            if (!$iRes = $oMail->send($aInfo, $sMsgHtml)) break;

            self::$_iTotalSent++;
        }
        unset($oMail);

        return ['status' => $iRes, 'nb_mail_sent' => self::$_iTotalSent];
    }

}
