<p>
    {lang 'Not yet registered on our affiliate program?'}<br />
    <strong><a href="{{ $design->url('affiliate','signup','step1') }}">{lang 'Join TODAY'}</a></strong> {lang 'and start making money!'}
</p>

{{ LoginForm::display() }}

<p>
    {{ LostPwdDesignCore::link('affiliate') }}
    {if Framework\Mvc\Model\DbConfig::getSetting('affActivationType') == Registration::EMAIL_ACTIVATION}
        | <a rel="nofollow" href="{{ $design->url('affiliate','home','resendactivation') }}">{lang 'Resend activation email'}</a>
    {/if}
</p>
