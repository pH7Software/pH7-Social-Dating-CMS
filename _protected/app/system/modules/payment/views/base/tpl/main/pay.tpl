<div class="center">

  {{ $is_paypal = $config->values['module.setting']['paypal.enable'] }}
  {{ $is_stripe = $config->values['module.setting']['stripe.enable'] }}
  {{ $is_2co = $config->values['module.setting']['2co.enable'] }}
  {{ $is_ccbill = $config->values['module.setting']['ccbill.enable'] }}

  {if !$is_paypal && !$is_stripe && !$is_2co && !$is_ccbill}
      <p class="err_msg">{lang 'No Payment System Enabled!'}</p>
  {else}

      {if $membership->enable == 1 && $membership->price != 0}
          {{ $oDesign = new PaymentDesign }}

          {if $is_paypal}
              <div class="paypal_logo left"><img src="{url_tpl_mod_img}big_paypal.gif" alt="PayPal" title="{lang 'Buy with PayPal on %site_name%'}" /></div>
          {/if}

          {if $is_paypal}
              <div class="left vs_marg">
                {{ $oDesign->buttonPayPal($membership) }}
              </div>
          {/if}

          {if $is_stripe}
              <div class="left vs_marg">
                {{ $oDesign->buttonStripe($membership) }}
              </div>
          {/if}

          {if $is_2co}
              <div class="left vs_marg">
                {{ $oDesign->button2CheckOut($membership) }}
              </div>
          {/if}

          {if $is_ccbill}
              <div class="left vs_marg">
                {{ $oDesign->buttonCCBill($membership) }}
              </div>
          {/if}

      {else}
          <p class="err_msg">{lang 'Membership requested is not available!'}</p>
      {/if}

  {/if}

</div>
