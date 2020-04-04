<?php
/**
 * @title          Payment Design
 *
 * @author         Pierre-Henry Soria <hello@ph7cms.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class / Design
 */

namespace PH7;

use Braintree_ClientToken;
use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\Api as PaymentApi;
use Skeerel\Skeerel;
use stdClass;

class PaymentDesign extends Framework\Core\Core
{
    const DIV_CONTAINER_NAME = 'payment-form';
    const MAX_STRING_FIELD_LENGTH = 127;

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonPayPal(stdClass $oMembership)
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);

        $oPayPal
            ->param('business', $this->config->values['module.setting']['paypal.email'])
            ->param('custom', base64_encode($oMembership->groupId . '|' . $oMembership->price))// Use base64_encode() to discourage curious people
            ->param('amount', $oMembership->price)
            ->param('item_number', $oMembership->groupId)
            ->param('item_name', $this->setMaxValueLengthToField($this->registry->site_name . ' ' . $oMembership->name))
            ->param('no_note', 1)
            ->param('no_shipping', 1)
            ->param('currency_code', $this->config->values['module.setting']['currency_code'])
            ->param('tax_cart', $this->config->values['module.setting']['vat_rate'])
            ->param('return', Uri::get('payment', 'main', 'process', 'paypal'))
            ->param('rm', 2)// Auto redirection in POST data
            ->param('notify_url', Uri::get('payment', 'main', 'notification', 'PH7\PayPal,' . $oMembership->groupId))
            ->param('cancel_return', Uri::get('payment', 'main', 'membership', '?msg=' . t('The payment was aborted. No charge has been taken from your account.'), false));

        $this->displayGatewayForm($oPayPal, $oMembership->name, 'PayPal');

        unset($oPayPal, $oMembership);
    }

    /**
     * Generates Stripe payment form Stripe API.
     *
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonStripe(stdClass $oMembership)
    {
        $oStripe = new Stripe;

        $oStripe
            ->param('item_number', $oMembership->groupId)
            ->param('amount', $oMembership->price);

        echo
        '<form action="', $oStripe->getUrl(), '" method="post">',
            $oStripe->generate(),
            '<script
                src="', Stripe::JS_LIBRARY_URL, '" class="stripe-button"
                data-key="', $this->config->values['module.setting']['stripe.publishable_key'], '"
                data-name="', $this->registry->site_name, '"
                data-description="', $oMembership->name, '"
                data-amount="', Stripe::getAmount($oMembership->price), '"
                data-currency="', $this->config->values['module.setting']['currency_code'], '"
                data-allow-remember-me="true">
            </script>
        </form>';

        unset($oStripe);
    }

    /**
     * Generates Braintree payment form Braintree API.
     *
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonBraintree(stdClass $oMembership)
    {
        $fPrice = $oMembership->price;
        $sCurrency = $this->config->values['module.setting']['currency_code'];
        $sLocale = PH7_LANG_NAME;

        Braintree::init($this->config);
        $sClientToken = Braintree_ClientToken::generate();

        echo '<script src="', Braintree::JS_LIBRARY_URL, '"></script>';

        $oBraintree = new Braintree;
        $oBraintree
            ->param('item_number', $oMembership->groupId)
            ->param('amount', $fPrice);

        $this->displayGatewayForm($oBraintree, $oMembership->name, '<u>Braintree</u>');

        unset($oBraintree);

        echo '<script>';
        echo '$(function () {';
        echo "braintree.setup('$sClientToken', 'dropin', {";
        echo "container: '" . self::DIV_CONTAINER_NAME . "',";
        echo "paypal: {singleUse: true, amount: '$fPrice', currency: '$sCurrency', locale: '$sLocale'}";
        echo '})})';
        echo '</script>';
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function button2CheckOut(stdClass $oMembership)
    {
        $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);

        $o2CO
            ->param('sid', $this->config->values['module.setting']['2co.vendor_id'])
            ->param('id_type', 1)
            ->param('cart_order_id', $oMembership->groupId)
            ->param('merchant_order_id', $oMembership->groupId)
            ->param('c_prod', $oMembership->groupId)
            ->param('c_price', $oMembership->price)
            ->param('total', $oMembership->price)
            ->param('c_name', $this->registry->site_name . ' ' . $oMembership->name)
            ->param('tco_currency', $this->config->values['module.setting']['currency_code'])
            ->param('c_tangible', 'N')
            ->param('x_receipt_link_url', Uri::get('payment', 'main', 'process', '2co'));

        $this->displayGatewayForm($o2CO, $oMembership->name, '2CO');

        unset($o2CO);
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonSkeerel(stdClass $oMembership)
    {
        Skeerel::generateSessionStateParameter(Skeerel::DEFAULT_COOKIE_NAME);

        $sWebsiteId = $this->config->values['module.setting']['skeerel.website_id'];
        $sSessionState = \Skeerel\Util\Session::get(Skeerel::DEFAULT_COOKIE_NAME);
        $sJsLibrary = Skeerel::JS_LIBRARY_URL;
        $bSandboxMode = (bool)$this->config->values['module.setting']['sandbox.enabled'];
        $sPrice = $oMembership->price; // Decimal price format (e.g., 19.95)
        $sCurrencyCode = $this->config->values['module.setting']['currency_code'];
        $sRedirectUrl = Uri::get('payment', 'main', 'process', 'skeerel');

        echo <<<HTML
<script src="$sJsLibrary"
        id="skeerel-api-script"
        data-website-id="$sWebsiteId"
        data-state="$sSessionState"
        data-redirect-url="$sRedirectUrl"
        data-payment-test="$bSandboxMode"
        data-amount="$sPrice"
        data-currency="$sCurrencyCode">
</script>
HTML;
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonCCBill(stdClass $oMembership)
    {
        // Not implemented yet.
        // Feel free to contribute: https://github.com/pH7Software/pH7-Social-Dating-CMS
    }

    /**
     * @param PaymentApi $oPaymentProvider
     * @param string $sMembershipName
     * @param string $sProviderName The payment provider name.
     *
     * @return void HTML output,
     */
    private function displayGatewayForm(PaymentApi $oPaymentProvider, $sMembershipName, $sProviderName)
    {
        echo '<form action="', $oPaymentProvider->getUrl(), '" method="post">';

        if ($oPaymentProvider instanceof Braintree) {
            echo $this->getDivFormContainer();
        }

        echo $oPaymentProvider->generate();
        echo '<button class="btn btn-primary btn-md" type="submit" name="submit">', $this->buyTxt($sMembershipName, $sProviderName), '</button>';
        echo '</form>';
    }

    /**
     * Build a "buy text" message.
     *
     * @param string $sMembershipName Membership name (e.g., Platinum, Silver, ...).
     * @param string $sProviderName Provider name (e.g., PayPal, 2CO, ...).
     *
     * @return string
     */
    private function buyTxt($sMembershipName, $sProviderName)
    {
        return t('Buy %0% with %1%!', $sMembershipName, '<b>' . $sProviderName . '</b>');
    }

    /**
     * @return string
     */
    private function getDivFormContainer()
    {
        return '<div id="' . self::DIV_CONTAINER_NAME . '"></div>';
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    private function setMaxValueLengthToField($sValue)
    {
        return substr($sValue, 0, self::MAX_STRING_FIELD_LENGTH);
    }
}
