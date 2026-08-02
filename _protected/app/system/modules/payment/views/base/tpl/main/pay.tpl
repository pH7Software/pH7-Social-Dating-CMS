<div class="center">
    {{ $is_paypal = $config->values['module.setting']['paypal.enabled'] }}
    {{ $is_stripe = $config->values['module.setting']['stripe.enabled'] }}
    {{ $is_braintree = $config->values['module.setting']['braintree.enabled'] }}
    {{ $is_2co = $config->values['module.setting']['2co.enabled'] }}
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
                    {{ $oDesign->buttonPayPal($membership, $checkout_token) }}
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
