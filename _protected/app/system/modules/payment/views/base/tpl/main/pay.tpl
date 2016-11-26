<div class="center">

  {{ $is_paypal = $config->values['module.setting']['paypal.enabled'] }}
  {{ $is_stripe = $config->values['module.setting']['stripe.enabled'] }}
  {{ $is_2co = $config->values['module.setting']['2co.enabled'] }}
  {*
      Still in development. Fork the project on https://github.com/pH7Software/pH7-Social-Dating-CMS/ and contribute to it
      {{ $is_ccbill = $config->values['module.setting']['ccbill.enabled'] }}
  *}
  {{ $is_ccbill = false }} {* Has to be removed once ccbill will be totally integrated *}


  {if !$is_paypal AND !$is_stripe AND !$is_2co AND !$is_ccbill}
      <p class="err_msg">{lang 'No Payment System Enabled!'}</p>
  {else}

      {if $membership->enable == 1 AND $membership->price != 0}
          {{ $oDesign = new PaymentDesign }}

          {if $is_paypal}
              <div class="paypal_logo left"><img src="{url_tpl_mod_img}big_paypal.gif" alt="PayPal" title="{lang 'Purchase your subscription using PayPal'}" /></div>
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
