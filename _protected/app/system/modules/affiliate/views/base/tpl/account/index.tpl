<div class="center">
    <h3 class="underline">{lang 'Your Unique Referral Link'}</h3>
    {{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('affiliate','router','refer', $username), null, false) }}
    <p>&nbsp;</p>

    <p class="bold">{lang 'Your affiliate amount is <em>%1%%0%</em>', $amount, $currency_sign}</p>
    <hr />
    {if $amount >= $min_withdrawal}
        <p>{lang 'If you want, you can <a href="%0%">contact us</a> to request a payment.', $contact_url}</p>
    {else}
        <p>{lang 'Unfortunately, you cannot request a payment at this time. You must have a minimum of %1%%0%.', $min_withdrawal, $currency_sign}</p>
    {/if}
</div>
