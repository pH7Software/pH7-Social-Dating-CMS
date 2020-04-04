<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Newsletter / Form / Processing
 */

namespace PH7;

defined('PH7') or exit('Restricted access');

use PH7\Framework\Cookie\Cookie;
use PH7\Framework\Date\CDateTime;
use PH7\Framework\Http\Http;
use PH7\Framework\Ip\Ip;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Util\Various;
use Teapot\StatusCode;

class SubscriptionFormProcess extends Form
{
    public function __construct()
    {
        parent::__construct();

        $oSubscriberModel = new SubscriberModel;
        $sEmail = $this->httpRequest->post('email');
        $sName = $this->httpRequest->post('name');
        $bIsSubscriber = (new ExistsCoreModel)->email($sEmail, DbTableName::SUBSCRIBER);

        switch ($this->httpRequest->post('direction')) {
            case 'subscribe': {
                if (!$bIsSubscriber) {
                    $aData = [
                        'name' => $sName,
                        'email' => $sEmail,
                        'current_date' => (new CDateTime)->get()->dateTime('Y-m-d H:i:s'),
                        'ip' => Ip::get(),
                        'hash_validation' => Various::genRnd(null, UserCoreModel::HASH_VALIDATION_LENGTH),
                        'active' => SubscriberModel::INACTIVE_STATUS,
                        'affiliated_id' => (int)(new Cookie)->get(AffiliateCore::COOKIE_NAME)
                    ];

                    if ($this->sendMail($aData)) {
                        \PFBC\Form::setSuccess(
                            'form_subscription',
                            t('Please activate your subscription by clicking the activation link you received by email. If you can not find the email, please look in your SPAM FOLDER and mark as not spam.')
                        );
                        $oSubscriberModel->add($aData);
                    } else {
                        \PFBC\Form::setError('form_subscription', Form::errorSendingEmail());
                    }
                } else {
                    \PFBC\Form::setError('form_subscription', t('Oops! You are already subscribed to our newsletter.'));
                }
            } break;

            case 'unsubscribe': {
                if ($bIsSubscriber) {
                    $oSubscriberModel->unsubscribe($sEmail);
                    \PFBC\Form::setSuccess('form_subscription', t('Your subscription was successfully canceled.'));
                } else {
                    \PFBC\Form::setError(
                        'form_subscription',
                        t("We didn't find any subscribers with this email address.")
                    );
                }
            } break;

            default:
                Http::setHeadersByCode(StatusCode::BAD_REQUEST);
                exit('Bad Request Error!');
        }
        unset($oSubscriberModel);
    }

    /**
     * Send a email to confirm their email address.
     *
     * @param array $aData The data details.
     *
     * @return int Number of recipients who were accepted for delivery.
     */
    private function sendMail(array $aData)
    {
        $sActivateLink = Uri::get('newsletter', 'home', 'activate') . PH7_SH . $aData['email'] . PH7_SH . $aData['hash_validation'];

        $this->view->content = t('Hi %0%!', $aData['name']) . '<br />' .
            t("Welcome to %site_name%'s Subscription!") . '<br />' .
            t('Activation link: %0%.', '<a href="' . $sActivateLink . '">' . $sActivateLink . '</a>');

        $this->view->footer = t('You are receiving this email because we received a registration application with "%0%" email address for %site_name% (%site_url%).', $aData['email']) . '<br />' .
            t('If you think someone has used your email address without your knowledge to create an account on %site_name%, please contact us using our contact form available on our website.');

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/newsletter/registration.tpl',
            $aData['email']
        );

        $aInfo = [
            'subject' => t('Confirm you email address!'),
            'to' => $aData['email']
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }
}
