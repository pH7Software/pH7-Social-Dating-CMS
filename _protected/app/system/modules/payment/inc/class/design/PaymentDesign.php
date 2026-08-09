<?php

/**
 * @title          Payment Design
 *
 * @author         Pierre-Henry Soria <hello@ph7builder.com>
 * @copyright      (c) 2012-2019, Pierre-Henry Soria. All Rights Reserved.
 * @license        MIT License; See LICENSE.md and COPYRIGHT.md in the root directory.
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\Api as PaymentApi;

class PaymentDesign extends Framework\Core\Core
{
    public const DIV_CONTAINER_NAME = 'payment-form';
    public const MAX_STRING_FIELD_LENGTH = 127;
    private const BRAINTREE_FORM_ID = 'braintree-payment-form';

    /**
     * @return void
     */
    public function buttonPayPal(\stdClass $oMembership, string $sCheckoutReference)
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);
        $mVatRate = $this->config->values['module.setting']['vat_rate'];

        $oPayPal
            ->param('business', $this->config->values['module.setting']['paypal.email'])
            ->param('custom', $sCheckoutReference)
            ->param('amount', $oMembership->price)
            ->param('item_number', $oMembership->groupId)
            ->param('item_name', $this->setMaxValueLengthToField($this->registry->site_name . ' ' . $oMembership->name))
            ->param('no_note', 1)
            ->param('no_shipping', 1)
            ->param('currency_code', $this->config->values['module.setting']['currency_code'])
            ->param('notify_url', Uri::get('payment', 'main', 'notify', 'paypal'))
            ->param(
                'return',
                Uri::get('payment', 'main', 'result', 'paypal') .
                '?checkout_reference=' . rawurlencode($sCheckoutReference)
            )
            ->param('cancel_return', Uri::get('payment', 'main', 'membership', '?msg=' . t('The payment was aborted. No charge has been taken from your account.'), false));

        if ((float)$mVatRate > 0) {
            $oPayPal->param('tax_rate', $mVatRate);
        }

        $this->displayGatewayForm($oPayPal, $oMembership->name, 'PayPal');

        unset($oPayPal, $oMembership);
    }

    /**
     * Generates Stripe payment form Stripe API.
     *
     * @return void
     */
    public function buttonStripe(\stdClass $oMembership, string $sCheckoutToken)
    {
        $oStripe = new Stripe();
        $sTotalAmount = PaymentCheckout::getTotalAmount(
            $oMembership->price,
            $this->config->values['module.setting']['vat_rate']
        );

        $oStripe
            ->param(
                'checkout_reference',
                PaymentCheckout::createReference($oMembership->groupId, $sCheckoutToken)
            );

        echo '<form action="', $this->str->escapeAttribute($oStripe->getUrl()), '" method="post">',
        $oStripe->generate(),
        '<script
                src="', $this->str->escapeAttribute(Stripe::JS_LIBRARY_URL), '" class="stripe-button"
                data-key="', $this->str->escapeAttribute($this->config->values['module.setting']['stripe.publishable_key']), '"
                data-name="', $this->str->escapeAttribute($this->registry->site_name), '"
                data-description="', $this->str->escapeAttribute($oMembership->name), '"
                data-amount="', $this->str->escapeAttribute(Stripe::getAmount($sTotalAmount)), '"
                data-currency="', $this->str->escapeAttribute($this->config->values['module.setting']['currency_code']), '"
                data-allow-remember-me="true">
            </script>
        </form>';

        unset($oStripe);
    }

    /**
     * Generates Braintree payment form Braintree API.
     *
     * @return void
     */
    public function buttonBraintree(\stdClass $oMembership, string $sCheckoutToken)
    {
        $sTotalAmount = PaymentCheckout::getTotalAmount(
            $oMembership->price,
            $this->config->values['module.setting']['vat_rate']
        );
        $sCurrency = $this->config->values['module.setting']['currency_code'];
        $sLocale = PH7_LANG_NAME;

        try {
            Braintree::init($this->config);
            $sClientToken = Braintree::generateClientToken();
        } catch (\Throwable $oException) {
            error_log(sprintf('Braintree checkout initialization failed: %s', $oException->getMessage()));
            echo '<p class="alert alert-warning">',
            $this->str->escape(
                t('Braintree checkout is temporarily unavailable. Please use another payment method or contact the site owner.')
            ),
            '</p>';

            return;
        }

        echo '<script src="', $this->str->escapeAttribute(Braintree::JS_LIBRARY_URL), '"></script>';

        $oBraintree = new Braintree();
        $oBraintree
            ->param(
                'checkout_reference',
                PaymentCheckout::createReference($oMembership->groupId, $sCheckoutToken)
            );

        $this->displayGatewayForm($oBraintree, $oMembership->name, 'Braintree');

        unset($oBraintree);

        echo '<script>';
        echo '$(function () {';
        echo 'const oForm = document.getElementById(' . json_encode(self::BRAINTREE_FORM_ID) . ');';
        echo 'if (!oForm || !window.braintree || !braintree.dropin || typeof braintree.dropin.create !== "function") { return; }';
        echo 'const oDropinOptions = {';
        echo 'authorization: ' . json_encode($sClientToken) . ',';
        echo 'container: ' . json_encode('#' . self::DIV_CONTAINER_NAME) . ',';
        echo 'paypal: {flow: "checkout", amount: ' . json_encode($sTotalAmount) . ', currency: ' . json_encode($sCurrency) . ', locale: ' . json_encode($sLocale) . '}';
        echo '};';
        echo 'braintree.dropin.create(oDropinOptions, function (oError, oDropinInstance) {';
        echo 'if (oError || !oDropinInstance) { console.error(oError); return; }';
        echo 'oForm.addEventListener("submit", function (oEvent) {';
        echo 'oEvent.preventDefault();';
        echo 'oDropinInstance.requestPaymentMethod(function (oNonceError, oPayload) {';
        echo 'if (oNonceError || !oPayload || !oPayload.nonce) { console.error(oNonceError); return; }';
        echo 'let oNonceInput = oForm.querySelector(\'input[name="payment_method_nonce"]\');';
        echo 'if (!oNonceInput) {';
        echo 'oNonceInput = document.createElement("input");';
        echo 'oNonceInput.type = "hidden";';
        echo 'oNonceInput.name = "payment_method_nonce";';
        echo 'oForm.appendChild(oNonceInput);';
        echo '}';
        echo 'oNonceInput.value = oPayload.nonce;';
        echo 'HTMLFormElement.prototype.submit.call(oForm);';
        echo '});';
        echo '});';
        echo '});';
        echo '});';
        echo '</script>';
    }

    /**
     * @return void
     */
    public function button2CheckOut(\stdClass $oMembership, string $sCheckoutToken)
    {
        $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);
        $sCheckoutReference = PaymentCheckout::createReference($oMembership->groupId, $sCheckoutToken);
        $sTotalAmount = PaymentCheckout::getTotalAmount(
            $oMembership->price,
            $this->config->values['module.setting']['vat_rate']
        );

        $o2CO
            ->param('sid', $this->config->values['module.setting']['2co.vendor_id'])
            ->param('id_type', 1)
            ->param('cart_order_id', $oMembership->groupId)
            ->param('merchant_order_id', $sCheckoutReference)
            ->param('c_prod', $oMembership->groupId)
            ->param('c_price', $sTotalAmount)
            ->param('total', $sTotalAmount)
            ->param('c_name', $this->registry->site_name . ' ' . $oMembership->name)
            ->param('tco_currency', $this->config->values['module.setting']['currency_code'])
            ->param('c_tangible', 'N')
            ->param('x_receipt_link_url', Uri::get('payment', 'main', 'process', '2co'));

        $this->displayGatewayForm($o2CO, $oMembership->name, '2CO');

        unset($o2CO);
    }

    /**
     * @return void
     */
    public function buttonCCBill(\stdClass $oMembership, string $sCheckoutToken)
    {
        // Not implemented yet.
        // Feel free to contribute: https://github.com/pH7Software/pH7-Social-Dating-CMS
    }

    /**
     * @param string $sMembershipName
     * @param string $sProviderName   the payment provider name
     *
     * @return void HTML output,
     */
    private function displayGatewayForm(PaymentApi $oPaymentProvider, $sMembershipName, $sProviderName)
    {
        $sFormId = ($oPaymentProvider instanceof Braintree)
            ? ' id="' . self::BRAINTREE_FORM_ID . '"'
            : '';

        echo '<form action="', $this->str->escapeAttribute($oPaymentProvider->getUrl()), '" method="post"', $sFormId, '>';

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
     * @param string $sProviderName   Provider name (e.g., PayPal, 2CO, ...).
     *
     * @return string
     */
    private function buyTxt($sMembershipName, $sProviderName)
    {
        return t(
            'Buy %0% with %1%!',
            $this->str->escape($sMembershipName),
            '<b>' . $this->str->escape($sProviderName) . '</b>'
        );
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
