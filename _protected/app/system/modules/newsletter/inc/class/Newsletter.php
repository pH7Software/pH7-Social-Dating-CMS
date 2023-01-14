<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Inc / Class
 */

declare(strict_types=1);

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Mail\Mail;
use PH7\Framework\Mail\Mailable;
use PH7\Framework\Mvc\Request\Http;
use stdClass;

/** Reset the time limit and increase the memory **/
@set_time_limit(0);
@ini_set('memory_limit', '528M');

class Newsletter extends Core
{
    private const MAX_BULK_EMAIL_NUMBER = 200;
    private const SLEEP_SEC = 5;

    private const MEMBER_DATA_METHOD = 'getProfiles';
    private const SUBSCRIBER_DATA_METHOD = 'getSubscribers';

    private SubscriberModel $oSubscriberModel;

    private string $sSubscribersMethod;

    private static int $iTotalSent = 0;

    public function __construct()
    {
        parent::__construct();

        $this->oSubscriberModel = new SubscriberModel;
        $bOnlySubscribers = $this->httpRequest->postExists('only_subscribers');
        $this->sSubscribersMethod = $bOnlySubscribers ? self::SUBSCRIBER_DATA_METHOD : self::MEMBER_DATA_METHOD;
    }

    /**
     * Send the newsletter to subscribers.
     *
     * @return array ['status' => integer, 'nb_mail_sent' => integer]
     */
    public function sendMessages(): array
    {
        $iStatus = 0; // Default value

        $oSubscribers = $this->oSubscriberModel->{$this->sSubscribersMethod}();

        $oMail = new Mail;
        foreach ($oSubscribers as $oSubscriber) {
            if (!$this->isOptedInSubscriber($oSubscriber)) {
                continue; // Skip the subscribers who haven't opted-in
            }

            if (!$iStatus = $this->sendMail($oSubscriber, $oMail)) {
                break;
            }

            // Do not send all emails at the same time to avoid overloading the mail server.
            if (++self::$iTotalSent > self::MAX_BULK_EMAIL_NUMBER) {
                sleep(self::SLEEP_SEC);
            }
        }
        unset($oMail, $oSubscribers);

        return [
            'status' => $iStatus,
            'nb_mail_sent' => self::$iTotalSent
        ];
    }

    /**
     * Send the newsletter to the subscribers.
     *
     * @param stdClass $oSubscriber Subscriber data from the DB.
     * @param Mailable $oMailEngine
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     * @throws Framework\Mvc\Request\WrongRequestMethodException
     */
    private function sendMail(stdClass $oSubscriber, Mailable $oMailEngine): bool
    {
        $this->view->content = $this->httpRequest->post('body', Http::NO_CLEAN);

        $sHtmlMsg = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/newsletter/msg.tpl',
            $oSubscriber->email
        );

        $aInfo = [
            'subject' => $this->httpRequest->post('subject'),
            'to' => $oSubscriber->email,
            'to_name' => $oSubscriber->firstName
        ];

        return $oMailEngine->send($aInfo, $sHtmlMsg);
    }

    private function isOptedInSubscriber(stdClass $oSubscriber): bool
    {
        return $this->isMemberData($oSubscriber) &&
            !$this->oSubscriberModel->isNotification($oSubscriber->profileId, 'enableNewsletters');
    }

    private function isMemberData(): bool
    {
        return $this->sSubscribersMethod === self::MEMBER_DATA_METHOD;
    }
}
