<?php
/**
 * @title          Main Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2015, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 * @version        1.0
 */
namespace PH7;

use
PH7\Framework\Mvc\Model\DbConfig,
PH7\Framework\Mail\Mail;

class MainController extends Controller
{

    /**
     * @access protected Protected access for the AdminController class derived from this class.
     * @var object $oUserModel
     * @var object $oPayModel
     * @var string $sTitle
     */
    protected $oUserModel, $oPayModel, $sTitle, $iProfileId;

    /**
     * @access private
     * @var boolean $_bStatus Payment status. Default is failure (FALSE).
     */
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
        $this->sTitle = t('Payment Zone');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;
        $this->output();
    }

    public function membership()
    {
        $oMembershipModel = $this->oPayModel->getMemberships();

        if (empty($oMembershipModel))
        {
            $this->displayPageNotFound(t('No membership found!'));
        }
        else
        {
            $this->sTitle = t('Memberships List');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->memberships = $oMembershipModel;
            $this->output();
        }
    }

    public function pay($iMembershipId = null)
    {
        $iMembershipId = (int) $iMembershipId;

        $oMembershipModel = $this->oPayModel->getMemberships($iMembershipId);

        if (empty($iMembershipId) || empty($oMembershipModel))
        {
            $this->displayPageNotFound(t('No membership found!'));
        }
        else
        {
            // Adding the stylesheet for Gatway Logo
            $this->design->addCss(PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS, 'common.css');

            // Regenerate the session ID to prevent the session fixation
            $this->session->regenerateId();

            $this->sTitle = t('Pay!');
            $this->view->page_title = $this->sTitle;
            $this->view->h2_title = $this->sTitle;
            $this->view->membership = $oMembershipModel;
            $this->output();
        }
    }

    public function process($sProvider = '')
    {
        switch ($sProvider)
        {
            case 'paypal':
            {
                $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enable']);
                if ($oPayPal->valid() && $this->httpRequest->postExists('item_number') && $this->httpRequest->postExists('custom'))
                {
                    if ($this->oUserModel->updateMembership($this->httpRequest->post('item_number'), $this->httpRequest->post('custom', 'int'), $this->httpRequest->post('amount'), $this->dateTime->dateTime('Y-m-d H:i:s')))
                    {
                        $this->_bStatus = true; // Status is OK
                        // PayPal will call automatically the "notification()" method thanks its IPN feature and "notify_url" form attribute.
                    }
                }
                unset($oPayPal);
            }
            break;

            case 'stripe':
            {
                if ($this->httpRequest->postExists('stripeToken'))
                {
                    Framework\File\Import::lib('Service.Stripe.init'); // Import the Stripe library
                    \Stripe\Stripe::setApiKey($this->config->values['module.setting']['stripe.secret_key']);

                     $oCharge = \Stripe\Charge::create(
                        array(
                            'source' => $this->httpRequest->post('stripeToken'),
                            'email'    => $this->httpRequest->post('stripeEmail')
                        )
                    );

                    if ($this->oUserModel->updateMembership($this->httpRequest->post('item_number'), $this->httpRequest->post('member_id', 'int'), $this->httpRequest->post('amount'), $this->dateTime->dateTime('Y-m-d H:i:s')))
                    {
                        $this->_bStatus = true; // Status is OK
                        $this->notification('Stripe'); // Add info into the log file
                    }
                }
            }
            break;

            case '2co':
            {
                $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enable']);
                $sVendorId = $this->config->values['module.setting']['2co.vendor_id'];
                $sSecretWord = $this->config->values['module.setting']['2co.secret_word'];

                if ($o2CO->valid($sVendorId, $sSecretWord) && $this->httpRequest->postExists('sale_id'))
                {
                    if ($this->oUserModel->updateMembership($this->httpRequest->post('sale_id'), $this->iProfileId, $this->httpRequest->post('price'), $this->dateTime->dateTime('Y-m-d H:i:s')))
                    {
                        $this->_bStatus = true; // Status is OK
                        $this->notification('TwoCO'); // Add info into the log file
                    }
                }
                unset($o2CO);
            }
            break;

            case 'ccbill':
            {
                // In developing...
                // Contact us at <ph7software@gmail.com> if you want to help us develop the payment system CCBill
            }
            break;

            default:
                $this->displayPageNotFound(t('Provinder Not Found!'));
        }

        // Set the page titles
        $this->sTitle = ($this->_bStatus) ? t('Thank you!') : t('Error occurred!');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        if ($this->_bStatus)
        {
            $this->updateAffCom();
        }

        // Set the valid page
        $sPage = ($this->_bStatus) ? 'success' : 'error';
        $this->manualTplInclude($sPage . $this->view->getTplExt());

        // Output
        $this->output();
    }

    public function notification($sGatewayName = '')
    {
        // Save buyer information to a log file
        if ($sGatewayName == 'PayPal' || $sGatewayName == 'Stripe' || $sGatewayName == 'TwoCO' || $sGatewayName == 'CCBill')
        {
            $sGatewayName = 'PH7\\' . $sGatewayName;
            $this->log(new $sGatewayName(false), t('%0% payment was made with the following information:', $sGatewayName));
        }

        // Send a notification email
        $this->sendNotifyMail();
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
        if ($iAffId < 1) return; // If there is no valid ID, we stop the method.

        $iAmount = $this->oUserModel->readProfile($this->iProfileId)->price;
        $iAffCom = ($iAmount*$this->config->values['module.setting']['rate.user_membership_payment']/100);

        if ($iAffCom > 0)
            $this->oUserModel->updateUserJoinCom($iAffId, $iAffCom);
    }

    /**
     * Send a notification email to the admin about the payment (IPN -> Instant Payment Notification).
     *
     * @return integer Number of recipients who were accepted for delivery.
     */
    protected function sendNotifyMail()
    {
        $sTo = DbConfig::getSetting('adminEmail');
        $sBuyer = $this->session->get('member_first_name') . ' (' . $this->session->get('member_username') . ')';

        $this->view->intro = t('Hello!') . '<br />' . t('You have received a new Payment from %0%', $sBuyer);
        $this->view->date = t('Date of the payment: %0%', $this->dateTime->get()->date());
        $this->view->browser_info = t('User Browser info: %0%', $this->browser->getUserAgent());
        $this->view->ip = t('Ip of the buyer: %0%', $this->design->ip(null, false));
        $this->view->details_text = t('Please find all other details below');
        $this->view->details_data = print_r($_POST, true);

        $sMessageHtml = $this->view->parseMail(PH7_PATH_SYS . 'global/' . PH7_VIEWS . PH7_TPL_NAME . '/mail/sys/mod/payment/ipn.tpl', $sTo);

        $aInfo = [
            'to' => $sTo,
            'subject' => t('New Payment Received from %0%', $sBuyer)
        ];

        return (new Mail)->send($aInfo, $sMessageHtml);
    }

    /**
     * Create a Payment Log file.
     *
     * @param \PH7\Framework\Payment\Gateway\Api\Api $oProvider A provider class.
     * @param string $sMsg
     * @return void
     */
    protected function log(Framework\Payment\Gateway\Api\Api $oProvider, $sMsg)
    {
        if ($this->config->values['module.setting']['log_file.enable'])
        {
            $sLogTxt = $sMsg . Framework\File\File::EOL . Framework\File\File::EOL . Framework\File\File::EOL . Framework\File\File::EOL;
            $oProvider->saveLog($sLogTxt . print_r($_POST, true), $this->registry);
        }
    }

}
