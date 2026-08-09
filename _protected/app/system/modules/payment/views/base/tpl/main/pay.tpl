<div class="center">
    {{ $is_paypal = $payment_gateways['paypal'] }}
    {{ $is_stripe = $payment_gateways['stripe'] }}
    {{ $is_braintree = $payment_gateways['braintree'] }}
    {{ $is_2co = $payment_gateways['2co'] }}
    {*
         Still in development. Fork the project at https://github.com/pH7Software/pH7-Social-Dating-CMS/ and contribute to it,
         then, open a pull request :-)

         {{ $is_ccbill = $config->values['module.setting']['ccbill.enabled'] }}
     *}
    {{ $is_ccbill = false }} {* Has to be removed once CCBill will be totally integrated *}


    {if !$is_paypal AND !$is_stripe AND !$is_braintree AND !$is_2co AND !$is_ccbill}
        <p class="err_msg">{lang 'No Payment System Enabled!'}</p>
    {else}
        {if $is_purchasable_membership}
            {{ $oDesign = new PaymentDesign }}

            <div class="paypal_logo left">
                <img src="{url_tpl_mod_img}payment-icon.png" alt="Payment Gateways" title="{lang 'Purchase your subscription safely!'}" />
            </div>

            <p class="bold">
                {lang 'Total:'}
                {% escape($config->values['module.setting']['currency_sign']) %}{% $total_price %}
                {if $config->values['module.setting']['vat_rate'] > 0}
                    <span class="small">({lang 'including %0%% VAT', $config->values['module.setting']['vat_rate']})</span>
                {/if}
            </p>

            {if $is_braintree}
                <div class="left vs_marg">
                    {{ $oDesign->buttonBraintree($membership, $checkout_token) }}
                </div>
            {/if}

            {if $is_paypal}
                <div class="left vs_marg">
                    {{ $oDesign->buttonPayPal($membership, $paypal_checkout_reference) }}
                </div>
            {/if}

            {if $is_stripe}
                <div class="left vs_marg">
                    {{ $oDesign->buttonStripe($membership, $checkout_token) }}
                </div>
            {/if}

            {if $is_2co}
                <div class="left vs_marg">
                    {{ $oDesign->button2CheckOut($membership, $checkout_token) }}
                </div>
            {/if}

            {if $is_ccbill}
                <div class="left vs_marg">
                    {{ $oDesign->buttonCCBill($membership, $checkout_token) }}
                </div>
            {/if}
        {else}
            <p class="err_msg">{lang 'Membership requested is not available!'}</p>
        {/if}
    {/if}
</div>
