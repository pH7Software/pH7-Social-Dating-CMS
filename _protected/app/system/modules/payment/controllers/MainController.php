<?php
/**
 * @title          Main Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 * @version        1.4
 */

namespace PH7;

use Braintree_Transaction;
use DateInterval;
use DateTime;
use Exception;
use PH7\Framework\Cache\Cache;
use PH7\Framework\Core\Kernel;
use PH7\Framework\File\File;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Payment\Gateway\Api\Api as ApiInterface;
use stdClass;

class MainController extends Controller
{
    const PAYPAL_GATEWAY_NAME = 'paypal';
    const STRIPE_GATEWAY_NAME = 'stripe';
    const BRAINTREE_GATEWAY_NAME = 'braintree';
    const TWO_CHECKOUT_GATEWAY_NAME = '2co';
    const SKEEREL_GATEWAY_NAME = 'skeerel';
    const CCBILL_GATEWAY_NAME = 'ccbill';

    const REDIRECTION_DELAY = 4; // In seconds

    const PAYMENT_GATEWAYS = [
        PayPal::class,
        Braintree::class,
        Stripe::class,
        TwoCO::class
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

        $this->oUserModel = new AffiliateCoreModel;
        $this->oPayModel = new PaymentModel;
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
            $this->view->page_title = $this->view->h1_title = t('Memberships Plans');
            $this->view->memberships = $oMembershipData;
            $this->output();
        }
    }

    /**
     * @param null|int $iMembershipId
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
        switch ($sProvider) {
            case static::PAYPAL_GATEWAY_NAME:
                $this->paypalHandler();
                break;

            case static::STRIPE_GATEWAY_NAME:
                $this->stripeHandler();
                break;

            case static::BRAINTREE_GATEWAY_NAME:
                $this->braintreeHandler();
                break;

            case static::TWO_CHECKOUT_GATEWAY_NAME:
                $this->twoCheckOutHandler();
                break;

            case static::SKEEREL_GATEWAY_NAME:
                $this->skeerelHandler();
                break;

            case static::CCBILL_GATEWAY_NAME:
                // Still in developing...
                // You are more than welcome to contribute on GitHub: https://github.com/pH7Software/pH7-Social-Dating-CMS
                break;

            default:
                $this->paypalHandler(); // Default payment gateway
            //$this->displayPageNotFound(t('Provider Not Found!'));
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

    /**
     * @param string $sGatewayName
     * @param int $iItemNumber
     *
     * @return void
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    public function notification($sGatewayName = '', $iItemNumber = 0)
    {
        // Save buyer information to a log file
        if ($this->isValidPaymentGateway($sGatewayName)) {
            // Add payment info into the log file
            $this->log(
                new $sGatewayName(false),
                t('%0% payment was made with the following information:', $sGatewayName)
            );
        }

        // Send a notification email
        if (!empty($iItemNumber)) {
            $this->sendNotifyMail($iItemNumber);
        }
    }

    public function info()
    {
        $this->sTitle = t('Membership Details');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $oInfo = $this->oUserModel->getMembershipDetails($this->iProfileId);
        if ($this->isMembershipExpirable($oInfo)) {
            $oDate = new DateTime($oInfo->membershipDate);
            $oDate->add(new DateInterval(sprintf('P%dD', $oInfo->expirationDays)));
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
     * @return int Number of recipients who were accepted for delivery.
     *
     * @throws Framework\Layout\Tpl\Engine\PH7Tpl\Exception
     */
    private function sendNotifyMail($iMembershipId)
    {
        $sAdminEmail = DbConfig::getSetting('adminEmail');
        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);

        $sUsername = $this->session->get('member_username');
        $sProfileLink = ' (' . $this->design->getProfileLink($sUsername, false) . ')';
        $sBuyer = $this->session->get('member_first_name') . $sProfileLink;

        $this->view->intro = t('Hello!') . '<br />' . t('Congratulations! You received a new payment from %0%', $sBuyer);
        $this->view->date = t('Date of the payment: %0%', $this->dateTime->get()->date());
        $this->view->membership_name = t('Membership name: %0%', $oMembershipData->name);
        $this->view->membership_price = t('Amount: %1%%0%', $oMembershipData->price, $this->config->values['module.setting']['currency_sign']);
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

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    private function paypalHandler()
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);
        if ($oPayPal->valid() && $this->httpRequest->postExists('custom')) {
            $aData = explode('|', base64_decode($this->httpRequest->post('custom')));
            $iItemNumber = (int)$aData[0];
            if ($this->oUserModel->updateMembership(
                $iItemNumber,
                $this->iProfileId,
                $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
            )) {
                $this->bStatus = true; // Status is OK
                $this->updateUserGroupId($iItemNumber);
                // PayPal automatically calls the `notification()` method thanks to its IPN feature and "notify_url" form attribute
            }
        }
    }

    private function stripeHandler()
    {
        if ($this->httpRequest->postExists('stripeToken')) {
            \Stripe\Stripe::setApiKey($this->config->values['module.setting']['stripe.secret_key']);
            $sAmount = $this->httpRequest->post('amount');

            try {
                $oCharge = \Stripe\Charge::create(
                    [
                        'amount' => Stripe::getAmount($sAmount),
                        'currency' => $this->config->values['module.setting']['currency_code'],
                        'source' => $this->httpRequest->post('stripeToken'),
                        'description' => t('Membership charged for %0%', $this->httpRequest->post('stripeEmail'))
                    ]
                );

                // Make sure the item has been paid
                if ($oCharge->paid === true) {
                    $iItemNumber = $this->httpRequest->post('item_number');
                    if ($this->oUserModel->updateMembership(
                        $iItemNumber,
                        $this->iProfileId,
                        $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
                    )) {
                        $this->bStatus = true; // Status is OK
                        $this->updateUserGroupId($iItemNumber);
                        $this->notification(Stripe::class, $iItemNumber);
                    }
                }
            } catch (\Stripe\Error\Card $oE) {
                // The card has been declined
                // Do nothing here as "$this->bStatus" is by default FALSE and so it will display "Error occurred" msg later
            } catch (\Stripe\Error\Base $oE) {
                $this->design->setMessage($this->str->escape($oE->getMessage(), true));
            }
        }
    }

    private function braintreeHandler()
    {
        if ($bNonce = $this->httpRequest->post('payment_method_nonce')) {
            Braintree::init($this->config);

            $oResult = Braintree_Transaction::sale([
                'amount' => $this->httpRequest->post('amount'),
                'paymentMethodNonce' => $bNonce,
                'options' => ['submitForSettlement' => true]
            ]);

            if ($oResult->success) {
                $iItemNumber = $this->httpRequest->post('item_number');
                if ($this->oUserModel->updateMembership(
                    $iItemNumber,
                    $this->iProfileId,
                    $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
                )) {
                    $this->bStatus = true; // Status is OK
                    $this->updateUserGroupId($iItemNumber);
                    $this->notification(Braintree::class, $iItemNumber);
                }
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

        $iItemNumber = $this->httpRequest->post('cart_order_id');
        if ($o2CO->valid($sVendorId, $sSecretWord)
            && $this->httpRequest->postExists('sale_id')
        ) {
            if ($this->oUserModel->updateMembership(
                $iItemNumber,
                $this->iProfileId,
                $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
            )
            ) {
                $this->bStatus = true; // Status is OK
                $this->updateUserGroupId($iItemNumber);
                $this->notification(TwoCO::class, $iItemNumber);
            }
        }
    }

    private function skeerelHandler()
    {
        try {
            if (\Skeerel\Skeerel::verifyAndRemoveSessionStateParameter(
                $this->httpRequest->get('state')
            )) {
                $oSkeerel = new \Skeerel\Skeerel(
                    $this->config->values['module.setting']['skeerel.website_id'],
                    $this->config->values['module.setting']['skeerel.website_secret']
                );
                $oResult = $oSkeerel->getData($this->httpRequest->get('token'));

                if ($oResult->status) {
                    $iItemNumber = $this->httpRequest->post('item_number');
                    if ($this->oUserModel->updateMembership(
                        $iItemNumber,
                        $this->iProfileId,
                        $this->dateTime->get()->dateTime(UserCoreModel::DATETIME_FORMAT)
                    )) {
                        $this->bStatus = true; // Status is OK
                        $this->updateUserGroupId($iItemNumber);
                        $this->notification(Skeerel::class, $iItemNumber);
                    }
                }
            } else {
                $this->design->setMessage(
                    t('Payment state parameter cannot be verified.')
                );
            }
        } catch (Exception $oE) {
            $this->design->setMessage($this->str->escape($oE->getMessage(), true));
        }
    }

    /**
     * Create a Payment Log file.
     *
     * @param ApiInterface $oProvider A provider class.
     * @param string $sMsg
     *
     * @return void
     */
    private function log(ApiInterface $oProvider, $sMsg)
    {
        if ($this->config->values['module.setting']['log_file.enabled']) {
            $sLogTxt = $sMsg . File::EOL . File::EOL . File::EOL;
            $oProvider->saveLog($sLogTxt . print_r($_POST, true), $this->registry);
        }
    }

    /**
     * Clear Membership cache.
     *
     * @return void
     */
    private function clearCache()
    {
        (new Cache)->start(
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
     * @param stdClass $oInfo
     *
     * @return bool
     */
    private function isMembershipExpirable(stdClass $oInfo)
    {
        return $oInfo->expirationDays != 0 && !empty($oInfo->membershipDate);
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
