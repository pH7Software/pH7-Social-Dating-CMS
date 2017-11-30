<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / pH7CMS Donation / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\PayPal;
use PH7\Framework\Url\Header;

class MainController extends Controller
{
    const HASH_VALIDATION = 'b9e67702a6f47dea30477d33160110934a16875c';
    const HASH_VALIDATION_START_POSITION = 3;
    const HASH_VALIDATION_LENGTH = 24;

    /** @var ValidateSiteModel */
    private $oValidateModel;

    public function __construct()
    {
        parent::__construct();

        $this->oValidateModel = new ValidateSiteModel;
    }

    public function donationBox()
    {
        $this->session->set(ValidateSiteCore::SESS_IS_VISITED, 1);
        $this->view->page_title = t('Will You Help pH7CMS?');

        $oPayPal = new PayPal();
        $oPayPal->param('business', base64_decode($this->config->values['module.setting']['paypal.donation_email']))
            ->param('currency_code', $this->config->values['module.setting']['currency'])
            ->param('cmd', '_donations')
            ->param('item_name', $this->config->values['module.setting']['donation.item_name'])
            ->param('amount', $this->config->values['module.setting']['donation.amount'])
            ->param('return', Uri::get('ph7cms-donation', 'main', 'validator', 'JkdjkPh7Pd5548OOSdgPU_92AIdO'));

        $this->view->form_action = $oPayPal->getUrl();
        $this->view->form_body = $oPayPal->generate();

        $this->output();
    }

    public function validator($sHash = null)
    {
        if (!empty($sHash) && $this->checkHash($sHash)) {
            if (!$this->oValidateModel->is()) {
                // Set the site to "validated" status
                $this->oValidateModel->set();

                DbConfig::clearCache();
            }

            Header::redirect(
                PH7_ADMIN_MOD,
                t('Thanks a LOT for your donation!!! Highly appreciate :-)'),
                Design::SUCCESS_TYPE
            );
        } else {
            Header::redirect(PH7_ADMIN_MOD);
        }
    }

    /**
     * @return bool
     */
    private function checkHash($sHash)
    {
        $sHash = substr($sHash, self::HASH_VALIDATION_START_POSITION, self::HASH_VALIDATION_LENGTH);

        return self::HASH_VALIDATION === sha1($sHash);
    }
}
