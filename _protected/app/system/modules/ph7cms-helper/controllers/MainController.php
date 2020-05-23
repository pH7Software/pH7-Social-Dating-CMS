<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / pH7CMS Helper / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
use PH7\Framework\Layout\Tpl\Engine\PH7Tpl\PH7Tpl;
use PH7\Framework\Mvc\Model\DbConfig;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\PayPal;
use PH7\Framework\Url\Header;

class MainController extends Controller
{
    const HASH_VALIDATION = 'JkdjkPh7Pd5548OOSdgPU_92AIdO';
    const INTERNAL_VERIFY_HASH = '681cd81b17b71c746e9ab7ac0445d3a3c960c329';
    const HASH_VALIDATION_START_POSITION = 3;
    const HASH_VALIDATION_LENGTH = 24;

    const VIEW_OPTIONS = [
        'donationbox',
        'upsetbox',
        'reviewbox',
        'githubbox',
        'stargithubbox',
        'forkgithubbox',
        'followgithubbox',
        'seemebox',
        'reviewboxrecall'
    ];

    const DONATION_AMOUNTS = [
        19,
        29,
        47,
        57,
        99,
        129,
        397,
        597
    ];

    /** @var ValidateSiteModel */
    private $oValidateModel;

    public function __construct()
    {
        parent::__construct();

        $this->oValidateModel = new ValidateSiteModel;
    }

    public function suggestionBox()
    {
        $this->setPageVisit();

        $this->view->page_title = t('Will You Help pH7CMS?');

        $sBoxType = $this->getSuggestionBox();
        if ($this->doesSuggestionBoxIsDonation($sBoxType)) {
            $oPayPal = new PayPal();
            $oPayPal->param('business', base64_decode($this->config->values['module.setting']['paypal.donation_email']))
                ->param('currency_code', $this->config->values['module.setting']['currency_code'])
                ->param('cmd', '_donations')
                ->param('item_name', $this->config->values['module.setting']['donation.item_name'])
                ->param('amount', self::DONATION_AMOUNTS[array_rand(self::DONATION_AMOUNTS)])
                ->param('return', Uri::get('ph7cms-helper', 'main', 'donationvalidator', self::HASH_VALIDATION));

            $this->view->form_action = $oPayPal->getUrl();
            $this->view->form_body = $oPayPal->generate();
        } else {
            $this->injectCssFile();
        }
        $this->manualTplInclude($sBoxType . PH7Tpl::TEMPLATE_FILE_EXT);

        $this->output();
    }

    public function donationValidator($sHash = null)
    {
        if (!empty($sHash) && $this->donationCheckHash($sHash)) {
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
    private function donationCheckHash($sHash)
    {
        $sHash = substr($sHash, self::HASH_VALIDATION_START_POSITION, self::HASH_VALIDATION_LENGTH);

        return self::INTERNAL_VERIFY_HASH === sha1($sHash);
    }

    /**
     * @return string
     */
    private function getSuggestionBox()
    {
        if ($this->httpRequest->getExists('box') &&
            $this->doesViewExist($this->httpRequest->get('box'))
        ) {
            return $this->httpRequest->get('box');
        }

        return self::VIEW_OPTIONS[array_rand(self::VIEW_OPTIONS)];
    }

    private function injectCssFile()
    {
        $this->design->addCss(
            PH7_LAYOUT . PH7_SYS . PH7_MOD . $this->registry->module . PH7_SH . PH7_TPL . PH7_TPL_MOD_NAME . PH7_SH . PH7_CSS,
            ValidateSiteCore::SUGGESTION_BOX_CSS_FILENAME
        );
    }

    private function setPageVisit()
    {
        $this->session->set(ValidateSiteCore::SESS_IS_VISITED, 1);
    }

    /**
     * @param string $sBoxType
     *
     * @return bool
     */
    private function doesSuggestionBoxIsDonation($sBoxType)
    {
        return $sBoxType === self::VIEW_OPTIONS[0] || $sBoxType === self::VIEW_OPTIONS[1];
    }

    /**
     * @param string $sViewName
     *
     * @return bool
     */
    private function doesViewExist($sViewName)
    {
        return in_array($sViewName, self::VIEW_OPTIONS, true);
    }
}
