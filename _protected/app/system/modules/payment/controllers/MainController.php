<?php
/**
 * @title          Main Controller
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 * @version        1.4
 */

namespace PH7;

use PH7\Framework\Cache\Cache;
use PH7\Framework\File\File;
use PH7\Framework\Mail\Mail;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Payment\Gateway\Api\Api as ApiInterface;

class MainController extends Controller
{
    /** @var AffiliateCoreModel */
    protected $oUserModel;

    /** @var PaymentModel */
    protected $oPayModel;

    /** @var string */
    protected $sTitle;

    /** @var int */
    protected $iProfileId;

    /** @var bool Payment status. Default is failure (FALSE) */
    private $_bStatus = false;


    public function __construct()
    {
        parent::__construct();

        $this->oUserModel = new AffiliateCoreModel;
        $this->oPayModel = new PaymentModel;
        $this->iProfileId = $this->session->get('member_id');
    }

    public function index()
    {
        $this->view->page_title = $this->view->h1_title = t('Payment Zone');
        $this->output();
    }

    public function membership()
    {
        $oMembershipData = $this->oPayModel->getMemberships();

        if (empty($oMembershipData)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            $this->view->page_title = $this->view->h2_title = t('Memberships List');
            $this->view->memberships = $oMembershipData;
            $this->output();
        }
    }

    /**
     * @param null|integer $iMembershipId
     */
    public function pay($iMembershipId = null)
    {
        $iMembershipId = (int) $iMembershipId;

        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);

        if (empty($iMembershipId) || empty($oMembershipData)) {
            $this->displayPageNotFound(t('No membership found!'));
        } else {
            // Adding the stylesheet for Gatway Logo
            $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'common.css');

            // Regenerate the session ID to prevent the session fixation attack
            $this->session->regenerateId();

            $this->view->page_title = $this->view->h2_title = t('Pay!');
            $this->view->membership = $oMembershipData;
            $this->output();
        }
    }

    /**
     * @param string $sProvider
     */
    public function process($sProvider = '')
    {
        switch ($sProvider) {
            case 'paypal':
            {
                $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);
                if ($oPayPal->valid() && $this->httpRequest->postExists('custom')) {
                    $aData = explode('|', base64_decode($this->httpRequest->post('custom')));
                    $iItemNumber = (int) $aData[0];
                    if ($this->oUserModel->updateMembership(
                        $iItemNumber,
                        $this->iProfileId,
                        $this->dateTime->get()->dateTime('Y-m-d H:i:s'))
                    ) {
                        $this->_bStatus = true; // Status is OK
                        $this->updateUserGroupId($iItemNumber);
                        // PayPal will call automatically the "notification()" method thanks its IPN feature and "notify_url" form attribute.
                    }
                }
                unset($oPayPal);
            }
            break;

            case 'stripe':
            {
                if ($this->httpRequest->postExists('stripeToken')) {
                    \Stripe\Stripe::setApiKey($this->config->values['module.setting']['stripe.secret_key']);
                    $sAmount = $this->httpRequest->post('amount');

                    try {
                        $oCharge = \Stripe\Charge::create(
                            [
                                'amount' => Stripe::getAmount($sAmount),
                                'currency' => $this->config->values['module.setting']['currency'],
                                'source' => $this->httpRequest->post('stripeToken'),
                                'description'    => 'Membership charged for ' . $this->httpRequest->post('stripeEmail')
                            ]
                        );

                        $iItemNumber = $this->httpRequest->post('item_number');
                        if ($this->oUserModel->updateMembership(
                            $iItemNumber,
                            $this->iProfileId,
                            $this->dateTime->get()->dateTime('Y-m-d H:i:s'))
                        ) {
                            $this->_bStatus = true; // Status is OK
                            $this->updateUserGroupId($iItemNumber);
                            $this->notification('Stripe', $iItemNumber); // Add info into the log file
                        }
                    }
                    catch (\Stripe\Error\Card $oE) {
                        // The card has been declined
                        // Do nothing here as "$this->_bStatus" is by default FALSE and so it will display "Error occurred" msg later
                    }
                    catch (\Stripe\Error\Base $oE) {
                        $this->design->setMessage( $this->str->escape($oE->getMessage(), true) );
                    }
                }
            }
            break;

            case '2co':
            {
                $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);
                $sVendorId = $this->config->values['module.setting']['2co.vendor_id'];
                $sSecretWord = $this->config->values['module.setting']['2co.secret_word'];

                $iItemNumber = $this->httpRequest->post('cart_order_id');
                if ($o2CO->valid($sVendorId, $sSecretWord) && $this->httpRequest->postExists('sale_id')) {
                    if (
                        $this->oUserModel->updateMembership(
                        $iItemNumber,
                        $this->iProfileId,
                        $this->dateTime->get()->dateTime('Y-m-d H:i:s'))
                    ) {
                        $this->_bStatus = true; // Status is OK
                        $this->updateUserGroupId($iItemNumber);
                        $this->notification('TwoCO', $iItemNumber); // Add info into the log file
                    }
                }
                unset($o2CO);
            }
            break;

            case 'ccbill':
            {
                // Still in developing...
                // You are more than welcome to contribute on Github: https://github.com/pH7Software/pH7-Social-Dating-CMS
            }
            break;

            default:
                $this->displayPageNotFound(t('Provinder Not Found!'));
        }

        // Set the page titles
        $this->sTitle = ($this->_bStatus) ? t('Thank you!') : t('Error occurred!');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        if ($this->_bStatus) {
            $this->updateAffCom();
            $this->clearCache();
        }

        // Set the valid page
        $sPage = ($this->_bStatus) ? 'success' : 'error';
        $this->manualTplInclude($sPage . $this->view->getTplExt());

        // Output
        $this->output();
    }

    /**
     * @param string  $sGatewayName
     * @param integer $iItemNumber
     *
     * @return void
     */
    public function notification($sGatewayName = '', $iItemNumber = 0)
    {
        // Save buyer information to a log file
        if ($sGatewayName == 'PayPal' || $sGatewayName == 'Stripe' || $sGatewayName == 'TwoCO' || $sGatewayName == 'CCBill') {
            $sGatewayName = 'PH7\\' . $sGatewayName;
            $this->log(new $sGatewayName(false), t('%0% payment was made with the following information:', $sGatewayName));
        }

        // Send a notification email
        if (!empty($iItemNumber)) {
            $this->sendNotifyMail($iItemNumber);
        }
    }

    public function info()
    {
        $this->sTitle = t('Details of the membership');
        $this->view->page_title = $this->view->h2_title = $this->sTitle;

        $oInfo = $this->oUserModel->getMembershipDetails($this->iProfileId);
        if ($oInfo->expirationDays != 0 && !empty($oInfo->membershipDate)) {
            $oDate = new \DateTime($oInfo->membershipDate);
            $oDate->add(new \DateInterval( sprintf('P%dD', $oInfo->expirationDays)) );
            $this->view->expirationDate = $oDate->format($this->config->values['language.application']['date_time_format']);
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
    protected function updateAffCom()
    {
        // Load the Affiliate config file
        $this->config->load(PH7_PATH_SYS_MOD . 'affiliate' . PH7_DS . PH7_CONFIG . PH7_CONFIG_FILE);

        $iAffId = $this->oUserModel->getAffiliatedId($this->iProfileId);
        if ($iAffId < 1) {
            // If there is no valid ID, we stop the method
            return;
        }

        $iAmount = $this->oUserModel->readProfile($this->iProfileId)->price;
        $iAffCom = ($iAmount*$this->config->values['module.setting']['rate.user_membership_payment']/100);

        if ($iAffCom > 0) {
            $this->oUserModel->updateUserJoinCom($iAffId, $iAffCom);
        }
    }

    /**
     * Send a notification email to the admin about the payment (IPN -> Instant Payment Notification).
     *
     * @param integer $iMembershipId
     *
     * @return integer Number of recipients who were accepted for delivery.
     */
    protected function sendNotifyMail($iMembershipId)
    {
        $oMembershipData = $this->oPayModel->getMemberships($iMembershipId);

        $sTo = DbConfig::getSetting('adminEmail');

        $sUsername = $this->session->get('member_username');
        $sProfileLink = ' (' . $this->design->getProfileLink($sUsername, false) . ')';
        $sBuyer = $this->session->get('member_first_name') . $sProfileLink;

        $this->view->intro = t('Hello!') . '<br />' . t('Congratulation! You received a new payment from %0%', $sBuyer);
        $this->view->date = t('Date of the payment: %0%', $this->dateTime->get()->date());
        $this->view->membership_name = t('Membership name: %0%', $oMembershipData->name);
        $this->view->membership_price = t('Amount: %1%%0%', $oMembershipData->price, $this->config->values['module.setting']['currency_sign']);
        $this->view->membership_duration = nt('Membership duration: %n% day', 'Membership duration: %n% days', $oMembershipData->expirationDays);
        $this->view->browser_info = t('User Web browser info: %0%', $this->browser->getUserAgent());
        $this->view->ip = t('Buyer IP address: %0%', $this->design->ip(null, false));

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_MAIL_NAME . '/tpl/mail/sys/mod/payment/ipn.tpl', $sTo);

        $aInfo = [
            'to' => $sTo,
            'subject' => t('New Payment Received from %0%', $sBuyer)
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * Create a Payment Log file.
     *
     * @param ApiInterface $oProvider A provider class.
     * @param string $sMsg
     *
     * @return void
     */
    protected function log(ApiInterface $oProvider, $sMsg)
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
    protected function clearCache()
    {
        (new Cache)->start(UserCoreModel::CACHE_GROUP, 'membershipdetails' . $this->iProfileId, null)->clear();
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
}
