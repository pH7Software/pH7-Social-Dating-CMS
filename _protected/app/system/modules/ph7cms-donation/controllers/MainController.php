<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2017, Pierre-Henry Soria. All Rights Reserved.
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
    const HASH_VALIDATION = '681cd81b17b71c746e9ab7ac0445d3a3c960c329';
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
        // Display the form box only if the site isn't validated yet
        if (!$this->oValidateModel->is()) {
            $this->session->set(ValidateSiteCore::SESS_IS_VISITED, 1);
            $this->view->page_title = t('Will You Help pH7CMS?');

            $oPayPal = new PayPal();
            $oPayPal->param('business', $this->config->values['module.setting']['paypal.donation_email'])
                ->param('currency_code', $this->config->values['module.setting']['currency'])
                ->param('cmd', '_donations')
                ->param('item_name', 'pH7CMS Contribution')
                ->param('amount', '39.00')
                ->param('return', Uri::get('ph7cms-donation', 'main', 'validator', 'HASH'));

                $this->view->form_action = $oPayPal->getUrl();
                $this->view->form_body = $oPayPal->generate();

                $this->output();
        }
    }

    public function validator($sHash = null)
    {
        if ($this->oValidateModel->is()) {
            Header::redirect(
                PH7_ADMIN_MOD,
                t('Your site is already validated!'),
                Design::SUCCESS_TYPE
            );
        } elseif (!empty($sHash) && $this->checkHash($sHash)) {
            // Set the site to "validated" status
            $this->oValidateModel->set();

            DbConfig::clearCache();

            Header::redirect(
                PH7_ADMIN_MOD,
                t('Thanks a LOT for your donation! Highly appreviate.'),
                Design::SUCCESS_TYPE
            );
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
