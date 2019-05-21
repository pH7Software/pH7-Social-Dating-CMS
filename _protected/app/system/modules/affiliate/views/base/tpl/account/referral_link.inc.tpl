<h3 class="underline">
    {lang 'Your Unique Referral Link'}
</h3>

{{ ShareUrlCoreForm::display(Framework\Mvc\Router\Uri::get('affiliate','router','refer', $username), null, false) }}
