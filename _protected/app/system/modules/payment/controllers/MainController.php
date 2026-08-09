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
use PH7\Framework\Str\Str;
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

    private float $fCompletedMembershipAmount = 0.0;

    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new AffiliateCoreModel();
        $this->oPayModel = new PaymentModel();
        $this->iProfileId = (int)$this->session->get('member_id');
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
            $bHasPaymentGateway = in_array(true, $this->getPaymentGateways(), true);
            foreach ($oMembershipData as $oMembership) {
                $oMembership->isPurchasable = $bHasPaymentGateway
                    && PaymentCheckout::isPurchasableMembership($oMembership);
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
            $aPaymentGateways = $this->getPaymentGateways();
            $this->view->is_purchasable_membership = in_array(true, $aPaymentGateways, true)
                && PaymentCheckout::isPurchasableMembership($oMembershipData);
            if ($this->view->is_purchasable_membership) {
                $this->view->checkout_token = (new Token())->generate(
                    PaymentCheckout::getTokenName($iMembershipId)
                );
                $sTotalAmount = $this->getTotalAmount($oMembershipData);
                $this->view->total_price = $sTotalAmount;
                $this->session->set(
                    PaymentCheckout::getContextName($iMembershipId),
                    $this->getCheckoutContext($oMembershipData)
                );

                if ($aPaymentGateways[self::PAYPAL_GATEWAY_NAME]) {
                    $sPayPalCheckoutReference = $this->createPayPalCheckoutReference(
                        $oMembershipData,
                        $sTotalAmount
                    );
                    if ($sPayPalCheckoutReference === null) {
                        $aPaymentGateways[self::PAYPAL_GATEWAY_NAME] = false;
                        $this->design->setMessage(
                            t('PayPal checkout is temporarily unavailable. Please use another configured gateway or contact the site owner.')
                        );
                    } else {
                        $this->view->paypal_checkout_reference = $sPayPalCheckoutReference;
                    }
                }

                $this->view->is_purchasable_membership = in_array(true, $aPaymentGateways, true);
            }
            $this->view->payment_gateways = $aPaymentGateways;
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
            $this->updateAffiliateCommission($this->fCompletedMembershipAmount);
            $this->clearCache();
        }

        // Set the valid template page
        $this->manualTplInclude($this->getTemplatePageName() . PH7Tpl::TEMPLATE_FILE_EXT);

        if ($this->bStatus) {
            $this->setAutoRedirectionToHomepage();
        }

        $this->output();
    }

    public function notify(string $sProvider = ''): void
    {
        if ($sProvider !== self::PAYPAL_GATEWAY_NAME || $this->httpRequest->getMethod() !== 'POST') {
            $this->sendPaymentNotificationResponse(400);
        }

        $mCheckoutReference = $_POST['custom'] ?? null;
        if (!is_string($mCheckoutReference) || strlen($mCheckoutReference) > 127) {
            $this->sendPaymentNotificationResponse(400);
        }

        try {
            $oCheckout = $this->oPayModel->getPayPalCheckout($mCheckoutReference);
            if ($oCheckout === null) {
                $this->sendPaymentNotificationResponse(200);
            }

            $oPayPal = new PayPal((bool)(int)$oCheckout->sandbox);
            if (!$oPayPal->valid()) {
                $this->sendPaymentNotificationResponse($oPayPal->hasTransportError() ? 503 : 200);
            }

            if (!PaymentCheckout::isValidPayPalNotification(
                $_POST,
                (string)$oCheckout->checkout_reference_hash,
                (int)$oCheckout->membership_id,
                (string)$oCheckout->merchant_account,
                (string)$oCheckout->expected_currency,
                (string)$oCheckout->expected_amount
            )) {
                $this->sendPaymentNotificationResponse(200);
            }

            $iPaymentResult = $this->oPayModel->completePayPalCheckout(
                $mCheckoutReference,
                trim((string)$_POST['txn_id']),
                $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
            );
            if ($iPaymentResult === PaymentModel::PAYMENT_COMPLETED) {
                $this->runPayPalCompletionHooks($oCheckout);
            }

            $this->sendPaymentNotificationResponse(200);
        } catch (\Throwable) {
            // A retryable response lets PayPal resend a verified notification after a transient failure.
            $this->sendPaymentNotificationResponse(503);
        }
    }

    public function result(): void
    {
        $this->view->page_title = $this->view->h2_title = t('Payment received');
        $sTemplateName = 'pending';
        $sCheckoutReference = $this->httpRequest->get('checkout_reference');
        if (is_string($sCheckoutReference) && $sCheckoutReference !== '') {
            try {
                $oCheckout = $this->oPayModel->getPayPalCheckout($sCheckoutReference);
                if (
                    $oCheckout instanceof \stdClass
                    && $oCheckout->status === 'completed'
                    && UserCore::auth()
                    && (int)$oCheckout->profile_id === $this->iProfileId
                ) {
                    $this->updateUserGroupId((int)$oCheckout->membership_id);
                    $this->clearCache();
                    $this->session->remove(
                        PaymentCheckout::getPayPalContextName((int)$oCheckout->membership_id)
                    );
                    $sTemplateName = 'success';
                }
            } catch (\Throwable) {
                // Keep the result page actionable while a transient status lookup is retried by PayPal.
            }
        }

        $this->manualTplInclude($sTemplateName . PH7Tpl::TEMPLATE_FILE_EXT);
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
     */
    private function updateAffiliateCommission(float $fMembershipAmount): void
    {
        // Load the Affiliate config file
        $this->config->load(PH7_PATH_SYS_MOD . 'affiliate' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        $iAffId = $this->oUserModel->getAffiliatedId($this->iProfileId);
        if ($iAffId < 1) {
            // If there is no valid ID, we stop the method
            return;
        }

        $fAffiliateCommission = $this->getAffiliateCommissionAmount($fMembershipAmount);

        if ($fAffiliateCommission > 0) {
            // If commission amount is higher than 0, we update the user's commission
            $this->oUserModel->updateUserJoinCom($iAffId, $fAffiliateCommission);
        }
    }

    /**
     * Send a notification email to the admin about the payment (IPN -> Instant Payment Notification).
     *
     * @param int $iMembershipId
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendNotifyMail($iMembershipId, string $sGatewayName): bool
    {
        $sAdminEmail = DbConfig::getSetting('adminEmail');
        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);
        $oProfile = $this->oUserModel->readProfile($this->iProfileId);
        if (!$oProfile instanceof \stdClass || !$oMembershipData instanceof \stdClass) {
            return false;
        }

        $sUsername = (string)$oProfile->username;
        $sFirstName = (string)$oProfile->firstName;
        $sProfileUrl = (new Str())->escapeAttribute((new UserCore())->getProfileLink($sUsername));
        $sBuyerHtml = escape($sFirstName) . ' (<a href="' . $sProfileUrl . '">' . escape($sUsername) . '</a>)';
        $sBuyerText = preg_replace('/[\r\n]+/', ' ', strip_tags($sFirstName . ' (' . $sUsername . ')'));
        if (!is_string($sBuyerText)) {
            return false;
        }

        $this->view->intro = t('Hello!') . '<br />' . t('Congratulations! You received a new payment from %0%', $sBuyerHtml);
        $this->view->date = escape(t('Date of the payment: %0%', $this->dateTime->get()->date()));
        $this->view->membership_name = escape(t('Membership name: %0%', $oMembershipData->name));
        $this->view->membership_price = escape(t(
            'Amount: %1%%0%',
            $this->getTotalAmount($oMembershipData),
            $this->config->values['module.setting']['currency_sign']
        ));
        $this->view->membership_duration = escape(nt('Membership duration: %n% day', 'Membership duration: %n% days', $oMembershipData->expirationDays));
        $this->view->browser_info = escape(t('Payment gateway: %0%', $this->getGatewayDisplayName($sGatewayName)));
        $this->view->ip = escape(t('Buyer account IP address: %0%', $oProfile->ip));

        $this->view->become_patron = t(
            'It might be now the perfect time to <a href="%0%">become a Patron</a> and support the development of the software?',
            (new Str())->escapeAttribute(Kernel::PATREON_URL)
        );

        $sMessageHtml = $this->view->parseMail(
            PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/payment/ipn.tpl',
            $sAdminEmail
        );

        $aInfo = [
            'to' => $sAdminEmail,
            'subject' => t('New Payment Received from %0%', $sBuyerText)
        ];

        return (new Mail())->send($aInfo, $sMessageHtml);
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
                $sFailureType = $this->isStripeCardDeclineException($oException) ? 'declined' : 'failed';
                error_log(sprintf('Stripe checkout %s: %s', $sFailureType, $oException->getMessage()));
                $this->setPaymentGatewayFailureMessage('Stripe');
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

            try {
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
                } else {
                    if ($oResult->transaction) {
                        error_log(sprintf(
                            'Braintree checkout declined: %s',
                            (string)$oResult->transaction->processorResponseText
                        ));
                    }
                    $this->setPaymentGatewayFailureMessage('Braintree');
                }
            } catch (\Throwable $oException) {
                error_log(sprintf('Braintree checkout failed: %s', $oException->getMessage()));
                $this->setPaymentGatewayFailureMessage('Braintree');
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
        $this->fCompletedMembershipAmount = (float)$oMembership->price;
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

        $this->sendNotifyMail($iMembershipId, $sGatewayName);
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
        return PaymentGatewayConfig::isReady($sProvider, $this->config->values['module.setting']);
    }

    private function getPaymentGateways(): array
    {
        return PaymentGatewayConfig::getAvailability($this->config->values['module.setting']);
    }

    private function createPayPalCheckoutReference(
        \stdClass $oMembership,
        string $sExpectedAmount
    ): ?string {
        try {
            $iMembershipId = (int)$oMembership->groupId;
            $sContextName = PaymentCheckout::getPayPalContextName($iMembershipId);
            $mExistingReference = $this->session->get($sContextName, false);
            if (is_string($mExistingReference) && PaymentCheckout::isPayPalReference($mExistingReference)) {
                $oExistingCheckout = $this->oPayModel->getPayPalCheckout($mExistingReference);
                if ($this->isReusablePayPalCheckout($oExistingCheckout, $oMembership, $sExpectedAmount)) {
                    return $mExistingReference;
                }
            }
            $this->session->remove($sContextName);

            $sCheckoutReference = PaymentCheckout::createPayPalReference();
            $bCreated = $this->oPayModel->createPayPalCheckout(
                $sCheckoutReference,
                (int)$this->iProfileId,
                $iMembershipId,
                (string)$oMembership->price,
                $sExpectedAmount,
                (string)$this->config->values['module.setting']['currency_code'],
                (string)$this->config->values['module.setting']['paypal.email'],
                (bool)$this->config->values['module.setting']['sandbox.enabled'],
                $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
            );

            if (!$bCreated) {
                return null;
            }

            $this->session->set($sContextName, $sCheckoutReference);

            return $sCheckoutReference;
        } catch (\Throwable) {
            return null;
        }
    }

    private function isReusablePayPalCheckout(
        mixed $mCheckout,
        \stdClass $oMembership,
        string $sExpectedAmount
    ): bool {
        return $mCheckout instanceof \stdClass
            && $mCheckout->status === 'pending'
            && (int)$mCheckout->profile_id === $this->iProfileId
            && (int)$mCheckout->membership_id === (int)$oMembership->groupId
            && PaymentCheckout::isExpectedAmount($oMembership->price, 0, $mCheckout->membership_amount)
            && PaymentCheckout::isExpectedAmount($sExpectedAmount, 0, $mCheckout->expected_amount)
            && strtoupper((string)$mCheckout->expected_currency) === strtoupper(
                (string)$this->config->values['module.setting']['currency_code']
            )
            && strcasecmp(
                trim((string)$mCheckout->merchant_account),
                trim((string)$this->config->values['module.setting']['paypal.email'])
            ) === 0
            && (bool)(int)$mCheckout->sandbox ===
                (bool)$this->config->values['module.setting']['sandbox.enabled'];
    }

    private function runPayPalCompletionHooks(\stdClass $oCheckout): void
    {
        $this->iProfileId = (int)$oCheckout->profile_id;
        $this->fCompletedMembershipAmount = (float)$oCheckout->membership_amount;

        try {
            $this->clearCache();
        } catch (\Throwable) {
            // A cache miss is safe; the database remains the source of truth.
        }

        try {
            $this->notifyPayment(PayPal::class, (int)$oCheckout->membership_id);
        } catch (\Throwable) {
            // Membership fulfillment is already committed; notification delivery must not trigger a duplicate payment.
        }

        try {
            $this->updateAffiliateCommission($this->fCompletedMembershipAmount);
        } catch (\Throwable) {
            // Affiliate bookkeeping must not make PayPal retry an already fulfilled transaction.
        }
    }

    private function sendPaymentNotificationResponse(int $iStatusCode): never
    {
        http_response_code($iStatusCode);
        if (!headers_sent()) {
            header('Content-Type: text/plain; charset=utf-8');
        }
        exit;
    }

    private function getGatewayDisplayName(string $sGatewayName): string
    {
        $iNamespacePosition = strrpos($sGatewayName, '\\');

        return $iNamespacePosition === false
            ? $sGatewayName
            : substr($sGatewayName, $iNamespacePosition + 1);
    }

    private function isStripeCardDeclineException(\Throwable $oException): bool
    {
        $sLegacyCardExceptionClass = 'Stripe\\Error\\Card';

        return is_a($oException, \Stripe\Exception\CardException::class)
            || (class_exists($sLegacyCardExceptionClass, false) && is_a($oException, $sLegacyCardExceptionClass));
    }

    private function setPaymentGatewayFailureMessage(string $sGatewayName): void
    {
        $this->design->setMessage(
            t(
                '%0% checkout could not be completed. Check your payment receipt before retrying, or contact the site owner.',
                $sGatewayName
            )
        );
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
        $oCache = new Cache();
        foreach (
            [
                'membershipDetails' . $this->iProfileId,
                'readProfile' . $this->iProfileId . DbTableName::MEMBER,
                'groupId' . $this->iProfileId . DbTableName::MEMBER
            ] as $sCacheId
        ) {
            $oCache->start(UserCoreModel::CACHE_GROUP, $sCacheId, null)->clear();
        }
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
    private function getAffiliateCommissionAmount(float $fMembershipAmount): float
    {
        return $fMembershipAmount *
            (float)$this->config->values['module.setting']['rate.user_membership_payment'] / 100;
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
