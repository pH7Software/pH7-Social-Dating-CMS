<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2018, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

namespace PH7;

use PH7\Framework\Mvc\Request\Http;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header as UrlHeader;

class BankForm
{
    public static function display()
    {
        if (isset($_POST['submit_bank_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_bank_account'])) {
                new BankFormProcess();
            }

            UrlHeader::redirect();
        }

        $oHR = new Http;
        $iProfileId = (self::isAdminLogged() && $oHR->getExists('profile_id')) ? $oHR->get('profile_id', 'int') : (new Session)->get('affiliate_id');
        $oAff = (new AffiliateModel)->readProfile($iProfileId, DbTableName::AFFILIATE);

        $oForm = new \PFBC\Form('form_bank_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new \PFBC\Element\Hidden('submit_bank_account', 'form_bank_account'));
        $oForm->addElement(new \PFBC\Element\Token('bank_account'));

        if (self::isAdminLogged() && $oHR->getExists('profile_id')) {
            $oForm->addElement(new \PFBC\Element\HTMLExternal('<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get('affiliate', 'admin', 'browse') . '">' . t('Back to Browse Affiliates') . '</a></p>'));
        }
        unset($oHR);

        $oForm->addElement(new \PFBC\Element\HTMLExternal('<h2 class="underline">' . t('Bank Information:') . '</h2>'));
        $sHtmlPayPalIcon = '<a href="https://www.paypal.com" rel="noopener" target="_blank"><img src="' . PH7_URL_STATIC . PH7_IMG . 'icon/paypal-small.svg" alt="PayPal" title="PayPal"></a><br />';
        $oForm->addElement(new \PFBC\Element\Email($sHtmlPayPalIcon . t('Your Bank Account:'), 'bank_account', ['id' => 'email_paypal', 'onblur' => 'CValid(this.value,this.id)', 'description' => t('Your Bank Account (PayPal Email Address).'), 'value' => $oAff->bankAccount, 'validation' => new \PFBC\Validation\BankAccount, 'required' => 1]));
        $oForm->addElement(new \PFBC\Element\HtmlExternal('<span class="input_error email_paypal"></span>'));
        $oForm->addElement(new \PFBC\Element\Button);
        $oForm->addElement(new \PFBC\Element\HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }

    /**
     * @return bool
     */
    private static function isAdminLogged()
    {
        return AdminCore::auth() && !Affiliate::auth();
    }
}
