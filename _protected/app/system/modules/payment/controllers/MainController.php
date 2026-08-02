<?php

/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2020, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\Core\Kernel;
use PH7\Framework\File\File;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Payment\Gateway\Api\Api as ApiInterface;
use PH7\Framework\Security\CSRF\Token;
use Stripe\Charge as StripeCharge;

class MainController extends Controller
{
    public const PAYPAL_GATEWAY_NAME = 'paypal';
    public const STRIPE_GATEWAY_NAME = 'stripe';
    public const BRAINTREE_GATEWAY_NAME = 'braintree';
    public const TWO_CHECKOUT_GATEWAY_NAME = '2co';
    public const CCBILL_GATEWAY_NAME = 'ccbill';

    public const REDIRECTION_DELAY = 4; // In seconds

    public const PAYMENT_GATEWAYS = [
        PayPal::class,
        Braintree::class,
        Stripe::class,
        TwoCO::class
    ];

    private const CHECKOUT_TOKEN_LIFETIME = 7200;

    private const GATEWAY_ENABLE_SETTINGS = [
        self::PAYPAL_GATEWAY_NAME => 'paypal.enabled',
        self::STRIPE_GATEWAY_NAME => 'stripe.enabled',
        self::BRAINTREE_GATEWAY_NAME => 'braintree.enabled',
        self::TWO_CHECKOUT_GATEWAY_NAME => '2co.enabled'
    ];

    private const SENSITIVE_PAYMENT_FIELDS = [
        'checkout_reference',
        'custom',
        'payment_method_nonce',
        'stripeToken'
    ];

    /** @var AffiliateCoreModel */
    protected $oUserModel;

    /** @var PaymentModel */
    protected $oPayModel;

    /** @var string */
    protected $sTitle;

    /** @var int */
    private $iProfileId;

    /** @var bool Payment status. Default is failure (FALSE) */
    private $bStatus = false;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new AffiliateCoreModel();
        $this->oPayModel = new PaymentModel();
        $this->iProfileId = $this->session->get('member_id');
    }

    public function index()
    {
        $this->view->page_title = $this->view->h1_title = t('Choose your package 🤩');
        $this->output();
    }

    public function membership()
    {
        $oMembershipData = $this->oPayModel->getMemberships();

        if (empty($oMembershipData)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            foreach ($oMembershipData as $oMembership) {
                $oMembership->isPurchasable = PaymentCheckout::isPurchasableMembership($oMembership);
                if ($oMembership->isPurchasable) {
                    $oMembership->totalPrice = $this->getTotalAmount($oMembership);
                }
            }

            $this->view->page_title = $this->view->h1_title = t('Memberships Plans');
            $this->view->memberships = $oMembershipData;
            $this->output();
        }
    }

    /**
     * @param int|null $iMembershipId
     *
     * @return void
     */
    public function pay($iMembershipId = null)
    {
        $iMembershipId = (int)$iMembershipId;

        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);

        if (empty($iMembershipId) || empty($oMembershipData)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            // Adding the stylesheet for gateway logos
            $this->design->addCss(
                PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
                'common.css'
            );

            // Regenerate the session ID to prevent the session fixation attack
            $this->session->regenerateId();

            $this->view->page_title = $this->view->h1_title = t('Payment Option');
            $this->view->membership = $oMembershipData;
            $this->view->is_purchasable_membership = PaymentCheckout::isPurchasableMembership($oMembershipData);
            if ($this->view->is_purchasable_membership) {
                $this->view->checkout_token = (new Token())->generate(
                    PaymentCheckout::getTokenName($iMembershipId)
                );
                $this->view->total_price = $this->getTotalAmount($oMembershipData);
                $this->session->set(
                    PaymentCheckout::getContextName($iMembershipId),
                    $this->getCheckoutContext($oMembershipData)
                );
            }
            $this->output();
        }
    }

    /**
     * @param string $sProvider
     *
     * @return void
     */
    public function process($sProvider = '')
    {
        if (!$this->isPaymentProviderEnabled($sProvider)) {
            $sProvider = '';
        }

        switch ($sProvider) {
            case self::PAYPAL_GATEWAY_NAME:
                $this->paypalHandler();
                break;
            case self::STRIPE_GATEWAY_NAME:
                $this->stripeHandler();
                break;
            case self::BRAINTREE_GATEWAY_NAME:
                $this->braintreeHandler();
                break;
            case self::TWO_CHECKOUT_GATEWAY_NAME:
                $this->twoCheckOutHandler();
                break;
            case self::CCBILL_GATEWAY_NAME:
                // Still in developing...
                // You are more than welcome to contribute on GitHub: https://github.com/pH7Software/pH7-Social-Dating-CMS
                break;
            default:
                break;
        }

        // Set the page titles
        $this->sTitle = $this->bStatus ? t('Thank you!') : t('Error occurred!');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        if ($this->bStatus) {
            $this->updateAffiliateCommission();
            $this->clearCache();
        }

        // Set the valid template page
        $this->manualTplInclude($this->getTemplatePageName() . PH7Tpl::TEMPLATE_FILE_EXT);

        if ($this->bStatus) {
            $this->setAutoRedirectionToHomepage();
        }

        $this->output();
    }

    public function info()
    {
        $this->sTitle = t('Membership Details');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $oInfo = $this->oUserModel->getMembershipDetails($this->iProfileId);
        if ($this->isMembershipExpirable($oInfo)) {
            $oDate = new \DateTime($oInfo->membershipDate);
            $oDate->add(new \DateInterval(sprintf('P%dD', $oInfo->expirationDays)));
            $this->view->expirationDate = $oDate->format($this->config->values['language.application']['textual_date_format']);
            unset($oDate);
        } else {
            $this->view->expirationDate = t('Never');
        }
        $this->view->membershipName = $oInfo->membershipName;
        unset($oInfo);

        $this->output();
    }

    /**
     * Update the Affiliate Commission.
     *
     * @return void
     */
    private function updateAffiliateCommission()
    {
        // Load the Affiliate config file
        $this->config->load(PH7_PATH_SYS_MOD . 'affiliate' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        $iAffId = $this->oUserModel->getAffiliatedId($this->iProfileId);
        if ($iAffId < 1) {
            // If there is no valid ID, we stop the method
            return;
        }

        $iAffCom = $this->getAffiliateCommissionAmount();

        if ($iAffCom > 0) {
            // If commission amount is higher than 0, we update the user's commission
            $this->oUserModel->updateUserJoinCom($iAffId, $iAffCom);
        }
    }

    /**
     * Send a notification email to the admin about the payment (IPN -> Instant Payment Notification).
     *
     * @param int $iMembershipId
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendNotifyMail($iMembershipId): bool
    {
        $sAdminEmail = DbConfig::getSetting('adminEmail');
        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);

        $sUsername = $this->session->get('member_username');
        $sProfileLink = ' (' . $this->design->getProfileLink($sUsername, false) . ')';
        $sBuyer = $this->session->get('member_first_name') . $sProfileLink;

        $this->view->intro = t('Hello!') . '<br />' . t('Congratulations! You received a new payment from %0%', $sBuyer);
        $this->view->date = t('Date of the payment: %0%', $this->dateTime->get()->date());
        $this->view->membership_name = t('Membership name: %0%', $oMembershipData->name);
        $this->view->membership_price = t(
            'Amount: %1%%0%',
            $this->getTotalAmount($oMembershipData),
            $this->config->values['module.setting']['currency_sign']
        );
        $this->view->membership_duration = nt('Membership duration: %n% day', 'Membership duration: %n% days', $oMembershipData->expirationDays);
        $this->view->browser_info = t('User Web browser info: %0%', $this->browser->getUserAgent());
        $this->view->ip = t('Buyer IP address: %0%', $this->design->ip(null, false));

        $this->view->become_patron = t('It might be now the perfect time to <a href="%0%">become a Patron</a> and support the development of the software?', Kernel::PATREON_URL);

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/payment/ipn.tpl',
            $sAdminEmail
        );

        $aInfo = [
            'to' => $sAdminEmail,
            'subject' => t('New Payment Received from %0%', $sBuyer)
        ];

        return (new Mail())->send($aInfo, $sMessageHtml);
    }

    private function paypalHandler()
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);
        if (!$this->httpRequest->postExists('custom') || !$oPayPal->valid()) {
            return;
        }

        $oMembership = $this->getCheckoutMembership($this->httpRequest->post('custom'));
        if ($oMembership !== null && $this->isValidPayPalPayment($oMembership)) {
            $this->completeMembershipPayment($oMembership, PayPal::class);
        }
    }

    private function stripeHandler()
    {
        if ($this->httpRequest->postExists(['stripeToken', 'checkout_reference'])) {
            $oMembership = $this->getCheckoutMembership(
                $this->httpRequest->post('checkout_reference')
            );
            if ($oMembership === null) {
                return;
            }

            \Stripe\Stripe::setApiKey($this->config->values['module.setting']['stripe.secret_key']);
            $sAmount = $this->getTotalAmount($oMembership);
            $sStripeEmail = $this->httpRequest->post('stripeEmail');

            try {
                $oCharge = StripeCharge::create(
                    [
                        'amount' => Stripe::getAmount($sAmount),
                        'currency' => $this->config->values['module.setting']['currency_code'],
                        'source' => $this->httpRequest->post('stripeToken'),
                        'description' => t('Membership charged for %0%', $sStripeEmail)
                    ]
                );

                if (
                    $oCharge->paid === true
                    && (string)$oCharge->amount === Stripe::getAmount($sAmount)
                    && strtolower((string)$oCharge->currency) === strtolower(
                        $this->config->values['module.setting']['currency_code']
                    )
                ) {
                    $this->completeMembershipPayment($oMembership, Stripe::class);
                }
            } catch (\Throwable $oException) {
                // Card declines are handled as payment failures (without exposing exception details).
                if (!$this->isStripeCardDeclineException($oException)) {
                    $this->design->setMessage($this->str->escape($oException->getMessage(), true));
                }
            }
        }
    }

    private function braintreeHandler()
    {
        if (
            $this->httpRequest->postExists(['payment_method_nonce', 'checkout_reference'])
            && $sNonce = $this->httpRequest->post('payment_method_nonce')
        ) {
            $oMembership = $this->getCheckoutMembership(
                $this->httpRequest->post('checkout_reference')
            );
            if ($oMembership === null) {
                return;
            }

            Braintree::init($this->config);

            $oResult = Braintree::sale([
                'amount' => $this->getTotalAmount($oMembership),
                'paymentMethodNonce' => $sNonce,
                'options' => ['submitForSettlement' => true]
            ]);

            if (
                $oResult->success
                && $oResult->transaction
                && PaymentCheckout::isExpectedAmount(
                    $oMembership->price,
                    $this->config->values['module.setting']['vat_rate'],
                    $oResult->transaction->amount
                )
                && strtoupper((string)$oResult->transaction->currencyIsoCode) === strtoupper(
                    $this->config->values['module.setting']['currency_code']
                )
            ) {
                $this->completeMembershipPayment($oMembership, Braintree::class);
            } elseif ($oResult->transaction) {
                $sErrMsg = t('Error processing transaction: %0%', $oResult->transaction->processorResponseText);
                $this->design->setMessage($this->str->escape($sErrMsg, true));
            }
        }
    }

    private function twoCheckOutHandler()
    {
        $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);
        $sVendorId = $this->config->values['module.setting']['2co.vendor_id'];
        $sSecretWord = $this->config->values['module.setting']['2co.secret_word'];

        if (!$o2CO->valid($sVendorId, $sSecretWord)) {
            return;
        }

        $oMembership = $this->getCheckoutMembership(
            $this->httpRequest->gets('merchant_order_id')
        );
        if (
            $oMembership !== null
            && PaymentCheckout::isExpectedAmount(
                $oMembership->price,
                $this->config->values['module.setting']['vat_rate'],
                $this->httpRequest->gets('total')
            )
        ) {
            $this->completeMembershipPayment($oMembership, TwoCO::class);
        }
    }

    private function getCheckoutMembership(mixed $mCheckoutReference): ?\stdClass
    {
        if (!is_string($mCheckoutReference)) {
            return null;
        }

        $aCheckoutReference = PaymentCheckout::parseReference($mCheckoutReference);
        if ($aCheckoutReference === null) {
            return null;
        }

        $iMembershipId = $aCheckoutReference['membership_id'];
        $oMembership = $this->oPayModel->getMemberships($iMembershipId);
        if (!$oMembership instanceof \stdClass || !PaymentCheckout::isPurchasableMembership($oMembership)) {
            return null;
        }

        $sContextName = PaymentCheckout::getContextName($iMembershipId);
        $mCheckoutContext = $this->session->get($sContextName, false);
        $bValidToken = (new Token())->check(
            PaymentCheckout::getTokenName($iMembershipId),
            $aCheckoutReference['token'],
            self::CHECKOUT_TOKEN_LIFETIME
        );
        $this->session->remove($sContextName);

        if (
            !$bValidToken
            || !is_string($mCheckoutContext)
            || !hash_equals($this->getCheckoutContext($oMembership), $mCheckoutContext)
        ) {
            return null;
        }

        return $oMembership;
    }

    private function isValidPayPalPayment(\stdClass $oMembership): bool
    {
        return PaymentCheckout::isValidPayPalPayment(
            $_POST,
            $oMembership,
            $this->config->values['module.setting']['paypal.email'],
            $this->config->values['module.setting']['currency_code'],
            $this->config->values['module.setting']['vat_rate']
        );
    }

    private function completeMembershipPayment(\stdClass $oMembership, string $sGatewayName): void
    {
        $iMembershipId = (int)$oMembership->groupId;
        if (!$this->oUserModel->updateMembership(
            $iMembershipId,
            $this->iProfileId,
            $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
        )) {
            return;
        }

        $this->bStatus = true;
        $this->updateUserGroupId($iMembershipId);
        $this->notifyPayment($sGatewayName, $iMembershipId);
    }

    private function notifyPayment(string $sGatewayName, int $iMembershipId): void
    {
        if ($this->isValidPaymentGateway($sGatewayName)) {
            $this->log(
                new $sGatewayName(false),
                t('%0% payment was made with the following information:', $sGatewayName)
            );
        }

        $this->sendNotifyMail($iMembershipId);
    }

    private function getTotalAmount(\stdClass $oMembership): string
    {
        return PaymentCheckout::getTotalAmount(
            $oMembership->price,
            $this->config->values['module.setting']['vat_rate']
        );
    }

    private function getCheckoutContext(\stdClass $oMembership): string
    {
        return $this->getTotalAmount($oMembership) . '|' . strtoupper(
            $this->config->values['module.setting']['currency_code']
        );
    }

    private function isPaymentProviderEnabled(string $sProvider): bool
    {
        $sSettingName = self::GATEWAY_ENABLE_SETTINGS[$sProvider] ?? null;

        return $sSettingName !== null && !empty($this->config->values['module.setting'][$sSettingName]);
    }

    private function isStripeCardDeclineException(\Throwable $oException): bool
    {
        $sLegacyCardExceptionClass = 'Stripe\\Error\\Card';

        return is_a($oException, \Stripe\Exception\CardException::class)
            || (class_exists($sLegacyCardExceptionClass, false) && is_a($oException, $sLegacyCardExceptionClass));
    }

    /**
     * Create a Payment Log file.
     *
     * @param ApiInterface $oProvider a provider class
     * @param string       $sMsg
     *
     * @return void
     */
    private function log(ApiInterface $oProvider, $sMsg)
    {
        if ($this->config->values['module.setting']['log_file.enabled']) {
            $sLogTxt = $sMsg . File::EOL . File::EOL . File::EOL;
            $aLogData = $_POST;
            foreach (self::SENSITIVE_PAYMENT_FIELDS as $sFieldName) {
                if (array_key_exists($sFieldName, $aLogData)) {
                    $aLogData[$sFieldName] = '[redacted]';
                }
            }
            $oProvider->saveLog($sLogTxt . print_r($aLogData, true), $this->registry);
        }
    }

    /**
     * Clear Membership cache.
     *
     * @return void
     */
    private function clearCache()
    {
        (new Cache())->start(
            UserCoreModel::CACHE_GROUP,
            'membershipDetails' . $this->iProfileId,
            null
        )->clear();
    }

    /**
     * @param int $iItemNumber
     *
     * @return void
     */
    private function updateUserGroupId($iItemNumber)
    {
        $this->session->set('member_group_id', $iItemNumber);
    }

    /**
     * @return bool
     */
    private function isMembershipExpirable(\stdClass $oInfo)
    {
        return $oInfo->expirationDays !== 0 && !empty($oInfo->membershipDate);
    }

    /**
     * @param string $sGatewayName
     *
     * @return bool
     */
    private function isValidPaymentGateway($sGatewayName)
    {
        return in_array($sGatewayName, self::PAYMENT_GATEWAYS, true);
    }

    /**
     * Get the affiliate's commission amount.
     *
     * @return float|int
     */
    private function getAffiliateCommissionAmount()
    {
        $iAmount = $this->oUserModel->readProfile($this->iProfileId)->price;

        return $iAmount * $this->config->values['module.setting']['rate.user_membership_payment'] / 100;
    }

    /**
     * Set automatic redirection to homepage if payment was successful.
     *
     * @return void
     */
    private function setAutoRedirectionToHomepage()
    {
        $this->design->setRedirect(
            $this->registry->site_url,
            null,
            null,
            self::REDIRECTION_DELAY
        );
    }

    /**
     * @return string
     */
    private function getTemplatePageName()
    {
        return $this->bStatus ? 'success' : 'error';
    }
}
