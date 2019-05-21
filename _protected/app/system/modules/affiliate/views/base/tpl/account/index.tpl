<div class="center">
    {manual_include 'referral_link.inc.tpl'}
    <p>&nbsp;</p>

    <p class="bold">
        {lang 'Your affiliate amount is <em>%1%%0% %2%</em>', $amount, $currency_sign, $currency_code}
    </p>
    <hr />

    {if $amount >= $min_withdrawal}
        <p>{lang 'If you want, you can <a href="%0%">contact us</a> to request a payment.', $contact_url}</p>
    {else}
        <p>
            {lang 'Unfortunately, you cannot request a payment at this time. You must have a minimum of %1%%0% %2%.', $min_withdrawal, $currency_sign, $currency_code}
        </p>
    {/if}
</div>
