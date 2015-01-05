<?php
/**
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Inc / Class
 */
namespace PH7;
defined('PH7') or exit('Restricted access');

use PH7\Framework\Mvc\Request\Http, PH7\Framework\Mail\Mail;

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
        $bOnlySubscribers = $this->httpRequest->postExists('only_subscribers');
        $iRes = 0; // Default value

        $sSubscribersMethod = ($bOnlySubscribers) ? 'getSubscribers' : 'getProfiles';
        $oSubscribers = $this->_oSubscriptionModel->$sSubscribersMethod();

        $oMail = new Mail;
        foreach ($oSubscribers as $oSubscriber)
        {
            $this->view->content = $this->httpRequest->post('body', Http::NO_CLEAN);

            $sMsgHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_NAME . '/mail/sys/mod/newsletter/msg.tpl', $oSubscriber->email);

            $aInfo = [
                'subject' => $this->httpRequest->post('subject'),
                'to' => $oSubscriber->email,
                'to_name' => $oSubscriber->firstName
            ];

            if (!$iRes = $oMail->send($aInfo, $sMsgHtml)) break;

            // Do not send all emails at the same time to avoid overloading the mail server.
            if (++self::$_iTotalSent > 250) sleep(10);
        }
        unset($oMail, $oSubscribers);

        return ['status' => $iRes, 'nb_mail_sent' => self::$_iTotalSent];
    }

}
