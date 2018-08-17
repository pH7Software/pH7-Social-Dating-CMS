<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2015-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / pH7CMS Helper / Controller
 */

namespace PH7;

use PH7\Framework\Layout\Html\Design;
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
    const VIEW_EXT = '.tpl';

    const VIEW_OPTIONS = [
        'donationbox',
        'reviewbox',
        'githubbox',
        'stargithubbox',
        'forkgithubbox',
        'followgithubbox',
        'seemebox'
    ];

    const DONATION_AMOUNTS = [
        10,
        19,
        29,
        39,
        49,
        77,
        99,
        120
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
        if ($sBoxType === self::VIEW_OPTIONS[0]) {
            $oPayPal = new PayPal();
            $oPayPal->param('business', base64_decode($this->config->values['module.setting']['paypal.donation_email']))
                ->param('currency_code', $this->config->values['module.setting']['currency'])
                ->param('cmd', '_donations')
                ->param('item_name', $this->config->values['module.setting']['donation.item_name'])
                ->param('amount', self::DONATION_AMOUNTS[mt_rand(0, count(self::DONATION_AMOUNTS) - 1)])
                ->param('return', Uri::get('ph7cms-helper', 'main', 'donationvalidator', self::HASH_VALIDATION));

            $this->view->form_action = $oPayPal->getUrl();
            $this->view->form_body = $oPayPal->generate();
        }

        $this->manualTplInclude($sBoxType . self::VIEW_EXT);

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

        return self::VIEW_OPTIONS[mt_rand(0, count(self::VIEW_OPTIONS) - 1)];
    }

    private function setPageVisit()
    {
        $this->session->set(ValidateSiteCore::SESS_IS_VISITED, 1);
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
