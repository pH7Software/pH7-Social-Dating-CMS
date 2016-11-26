<?php
/**
 * @title          Payment Design
 *
 * @author         Pierre-Henry Soria <ph7software@gmail.com>
 * @copyright      (c) 2012-2016, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; See PH7.LICENSE.txt and PH7.COPYRIGHT.txt in the root directory.
 * @package        PH7 / App / System / Module / Payment / Inc / Class / Design
 * @version        0.9
 */
namespace PH7;

use PH7\Framework\Mvc\Router\Uri;

class PaymentDesign extends Framework\Core\Core
{

    /**
     * @param object $oMembership The Object Membership Model.
     * @return void
     */
    public function buttonPayPal($oMembership)
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
            ->param('notify_url',  Uri::get('payment', 'main', 'notification', 'PayPal'))
            ->param('cancel_return', Uri::get('payment', 'main', 'pay', '?msg=' . t('The payment was aborted. No charge has been taken from your account.'), false));

        echo
        '<form action="', $oPayPal->getUrl(), '" method="post">',
            $oPayPal->generate(),
            '<button class="btn btn-primary btn-lg" type="submit" name="submit">', static::buyTxt($oMembership->name, 'PayPal'), '</button>
        </form>';

        unset($oPayPal, $oMembership);
    }

    /**
     * Generates Stripe Payment form thanks the Stripe API.
     *
     * @param object $oMembership The Object Membership Model.
     * @return void
     */
    public function buttonStripe($oMembership)
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
     * @param object $oMembership The Object Membership Model.
     * @return void
     */
    public function button2CheckOut($oMembership)
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

        echo
        '<form action="', $o2CO->getUrl(), '" method="post">',
            $o2CO->generate(),
            '<button class="btn btn-primary btn-lg" type="submit" name="submit">', static::buyTxt($oMembership->name, '2CO'), '</button>
        </form>';

        unset($o2CO);
    }

    /**
     * @param object $oMembership The Object Membership Model.
     * @return void
     */
    public function buttonCCBill($oMembership)
    {

    }

    /**
     * Build a "buy message".
     *
     * @param string $sMembershipName Membership name (e.g., Platinum, Silver, ...).
     * @param string $sProvider Provider name (e.g., PayPal, 2CO, ...).
     * @return string
     */
    protected static function buyTxt($sMembershipName, $sProvider)
    {
        return t('Buy %0% with %1%!', $sMembershipName, '<b>' . $sProvider . '</b>');
    }

}
