<?php
/**
 * @title          Payment Design
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class / Design
 */

namespace PH7;

use PH7\Framework\Mvc\Router\Uri;
use PH7\Framework\Payment\Gateway\Api\Api as PaymentApi;
use stdClass;

class PaymentDesign extends Framework\Core\Core
{
    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonPayPal(stdClass $oMembership)
    {
        $oPayPal = new PayPal($this->config->values['module.setting']['sandbox.enabled']);

        $oPayPal->param('business', $this->config->values['module.setting']['paypal.email'])
            ->param('custom', base64_encode($oMembership->groupId . '|' . $oMembership->price)) // Use base64_encode() to discourage curious people
            ->param('amount', $oMembership->price)
            ->param('item_number', $oMembership->groupId)
            ->param('item_name', $this->registry->site_name . ' ' . $oMembership->name)
            ->param('no_note', 1)
            ->param('no_shipping', 1)
            ->param('currency_code', $this->config->values['module.setting']['currency'])
            ->param('tax_cart', $this->config->values['module.setting']['vat_rate'])
            ->param('return', Uri::get('payment', 'main', 'process', 'paypal'))
            ->param('rm', 2) // Auto redirection in POST data
            ->param('notify_url',  Uri::get('payment', 'main', 'notification', 'PayPal,' . $oMembership->groupId))
            ->param('cancel_return', Uri::get('payment', 'main', 'membership', '?msg=' . t('The payment was aborted. No charge has been taken from your account.'), false));

        echo self::displayGatewayForm($oPayPal, $oMembership->name, 'PayPal');

        unset($oPayPal, $oMembership);
    }

    /**
     * Generates Stripe Payment form thanks the Stripe API.
     *
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonStripe(stdClass $oMembership)
    {
        $oStripe = new Stripe;

        $oStripe->param('item_number', $oMembership->groupId)
            ->param('amount', $oMembership->price);

        echo
        '<form action="', $oStripe->getUrl(), '" method="post">',
            $oStripe->generate(),
            '<script
                src="https://checkout.stripe.com/checkout.js" class="stripe-button"
                data-key="', $this->config->values['module.setting']['stripe.publishable_key'], '"
                data-name="', $this->registry->site_name, '"
                data-description="', $oMembership->name, '"
                data-amount="', Stripe::getAmount($oMembership->price), '"
                data-currency="', $this->config->values['module.setting']['currency'], '"
                data-allow-remember-me="true"
                data-bitcoin="true">
            </script>
        </form>';

        unset($oStripe);
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function button2CheckOut(stdClass $oMembership)
    {
        $o2CO = new TwoCO($this->config->values['module.setting']['sandbox.enabled']);

        $o2CO->param('sid', $this->config->values['module.setting']['2co.vendor_id'])
            ->param('id_type', 1)
            ->param('cart_order_id', $oMembership->groupId)
            ->param('merchant_order_id', $oMembership->groupId)
            ->param('c_prod', $oMembership->groupId)
            ->param('c_price', $oMembership->price)
            ->param('total', $oMembership->price)
            ->param('c_name', $this->registry->site_name . ' ' . $oMembership->name)
            ->param('tco_currency', $this->config->values['module.setting']['currency'])
            ->param('c_tangible', 'N')
            ->param('x_receipt_link_url', Uri::get('payment', 'main', 'process', '2co'));

        echo self::displayGatewayForm($o2CO, $oMembership->name, '2CO');

        unset($o2CO);
    }

    /**
     * @param stdClass $oMembership
     *
     * @return void
     */
    public function buttonCCBill(stdClass $oMembership)
    {
        // Not implemented yet.
        // Feel free to contribute on our open source repo: https://github.com/pH7Software/pH7-Social-Dating-CMS
    }

    /**
     * @param PaymentApi $oPaymentProvider
     * @param string $sMembershipName
     * @param string $sProviderName
     *
     * @return string
     */
    private function displayGatewayForm(PaymentApi $oPaymentProvider, $sMembershipName, $sProviderName)
    {
        echo '<form action="', $oPaymentProvider->getUrl(), '" method="post">',
            $oPaymentProvider->generate(),
            '<button class="btn btn-primary btn-md" type="submit" name="submit">', self::buyTxt($sMembershipName, $sProviderName), '</button>
        </form>';
    }

    /**
     * Build a "buy message".
     *
     * @param string $sMembershipName Membership name (e.g., Platinum, Silver, ...).
     * @param string $sProviderName Provider name (e.g., PayPal, 2CO, ...).
     *
     * @return string
     */
    private static function buyTxt($sMembershipName, $sProviderName)
    {
        return t('Buy %0% with %1%!', $sMembershipName, '<b>' . $sProviderName . '</b>');
    }
}
