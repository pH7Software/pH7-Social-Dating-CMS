<?php
/**
 * @title          Main Controller
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2013, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Controller
 * @version        1.0
 */
namespace PH7;

class MainController extends Controller
{

    /**
     * @access protected Protected access for the AdminController class derived from this class.
     * @var object $oPayModel
     * @var string $sTitle
     */
    protected $oPayModel, $sTitle;

    /**
     * @access private
     * @var boolean $_bStatus Payment status. Default is failure (FALSE).
     */
    private $_bStatus = false;

    public function __construct()
    {
        parent::__construct();

        $this->oPayModel = new PaymentModel;
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
                if ($oPayPal->valid() && $this->httpRequest->postExists('item_number'))
                {
                    if ((new UserCoreModel)->updateMembership($this->httpRequest->post('item_number'), $this->session->get('member_id'), $this->httpRequest->post('amount'), $this->dateTime->dateTime('Y-m-d H:i:s')))
                    {
                        $this->log($oPayPal, t('PayPal payment was made, the following information:'));
                        $this->_bStatus = true; // Status is OK
                    }
                }
                unset($oPayPal);
            }
            break;

            case '2co':
            {
                $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enable']);
                $sVendorId = $this->config->values['module.setting']['2co.vendor_id'];
                $sSecretWord = $this->config->values['module.setting']['2co.secret_word'];

                if ($o2CO->valid($sVendorId, $sSecretWord) && $this->httpRequest->postExists('sale_id'))
                {
                    if ((new UserCoreModel)->updateMembership($this->httpRequest->post('sale_id'), $this->session->get('member_id'), $this->httpRequest->post('price'), $this->dateTime->dateTime('Y-m-d H:i:s')))
                    {
                        $this->log($o2CO, t('2CheckOut payment was made, the following information:'));
                        $this->_bStatus = true; // Status is OK
                    }
                }
                unset($o2CO);
            }
            break;

            case 'ccbill':
            {
                // In developing...
                // Contact us at <developers.ph7software@gmail.com> if you want to help us develop the payment system CCBill
            }
            break;

            default:
                $this->displayPageNotFound(t('Provinder Not Found!'));
        }

        // Set the page titles
        $this->sTitle = ($this->_bStatus) ? t('Thank you!') : t('Error occurred!');
        $this->view->page_title = $this->sTitle;
        $this->view->h2_title = $this->sTitle;

        // Set the valid page
        $sPage = ($this->_bStatus) ? 'success' : 'error';
        $this->manualTplInclude($sPage . $this->view->getTplExt());

        // Output
        $this->output();
    }

    /**
     * @param object $oProvider A provider class.
     * @param string $sMsg
     * @return void
     */
    private function log($oProvider, $sMsg)
    {
        if ($this->config->values['module.setting']['log_file.enable'])
        {
            $sLogTxt = $sMsg . Framework\File\File::EOL . Framework\File\File::EOL . Framework\File\File::EOL . Framework\File\File::EOL;
            $oProvider->saveLog($sLogTxt . $_POST);
        }
    }

}
