<?php
/**
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2022, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 * @package        PH7 / App / System / Module / Affiliate / Form
 */

declare(strict_types=1);

namespace PH7;

use PFBC\Element\Button;
use PFBC\Element\Email;
use PFBC\Element\Hidden;
use PFBC\Element\HTMLExternal;
use PFBC\Element\Token;
use PFBC\Validation\BankAccount;
use PH7\Datatype\Type;
use PH7\Framework\Mvc\Request\Http as HttpRequest;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Session\Session;
use PH7\Framework\Url\Header as UrlHeader;

class BankForm
{
    public static function display(): void
    {
        $oHttpRequest = new HttpRequest;

        if (isset($_POST['submit_bank_account'])) {
            if (\PFBC\Form::isValid($_POST['submit_bank_account'])) {
                new BankFormProcess(self::getProfileId($oHttpRequest));
            }

            UrlHeader::redirect();
        }

        $oForm = new \PFBC\Form('form_bank_account');
        $oForm->configure(['action' => '']);
        $oForm->addElement(new Hidden('submit_bank_account', 'form_bank_account'));
        $oForm->addElement(new Token('bank_account'));

        if (self::isAdminLogged() && $oHttpRequest->getExists('profile_id')) {
            $oForm->addElement(
                new HTMLExternal(
                    '<p class="center"><a class="bold btn btn-default btn-md" href="' . Uri::get('affiliate', 'admin', 'browse') . '">' . t('Back to Browse Affiliates') . '</a></p>'
                )
            );
        }
        $oForm->addElement(
            new HTMLExternal(
                '<h2 class="underline">' . t('Bank Information:') . '</h2>'
            )
        );
        $oForm->addElement(
            new Email(
                AffiliateDesign::getPayPalIcon() . t('Your Bank Account:'),
                'bank_account',
                [
                    'id' => 'paypal_email',
                    'onblur' => 'CValid(this.value,this.id)',
                    'description' => t('Your Bank Account (PayPal Email Address).'),
                    'value' => self::getAffiliateBankAccount($oHttpRequest),
                    'validation' => new BankAccount,
                    'required' => 1
                ]
            )
        );
        $oForm->addElement(new HtmlExternal('<span class="input_error paypal_email"></span>'));
        $oForm->addElement(new Button);
        $oForm->addElement(new HTMLExternal('<script src="' . PH7_URL_STATIC . PH7_JS . 'validate.js"></script>'));
        $oForm->render();
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return string
     */
    private static function getAffiliateBankAccount(HttpRequest $oHttpRequest)
    {
        $iProfileId = self::getProfileId($oHttpRequest);

        return (new AffiliateModel)->readProfile(
            $iProfileId,
            DbTableName::AFFILIATE
        )->bankAccount;
    }

    /**
     * @param HttpRequest $oHttpRequest
     *
     * @return int|string
     */
    private static function getProfileId(HttpRequest $oHttpRequest)
    {
        if (self::isAdminLogged() && $oHttpRequest->getExists('profile_id')) {
            return $oHttpRequest->get('profile_id', Type::INTEGER);
        }

        return (new Session)->get('affiliate_id');
    }

    private static function isAdminLogged(): bool
    {
        return AdminCore::auth() && !Affiliate::auth();
    }
}
