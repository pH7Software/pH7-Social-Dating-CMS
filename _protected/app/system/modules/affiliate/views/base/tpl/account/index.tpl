<div class="center">
    <p class="bold">{lang 'Your affiliate URL is: <em><a href="%affiliate_url%">%affiliate_url%</a></em>'}</p>
    <p>&nbsp;</p>

    <p class="bold">{lang 'Your affiliate amount is: %1%%0%', $amount, $currency_sign}</p>
    {if $amount >= $min_withdrawal}
        <p>{lang 'If you want, you can <a href="%0%">contact us</a> to request a payment.', $contact_url}</p>
    {else}
        <p>{lang 'Unfortunately, you cannot request a payment at this time. You must have a minimum of %1%%0%.', $min_withdrawal, $currency_sign}</p>
    {/if}
</div>
