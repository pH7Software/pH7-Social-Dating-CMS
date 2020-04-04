<div class="col-md-8">
    <p>
        {lang 'Not registered yet?'}<br />
        <a class="underline" href="{{ $design->url('user','signup','step1') }}">
            <strong>{lang 'Join Us Today!'}</strong>
        </a>
    </p>

    {{ LoginForm::display() }}

    <p>
        {{ LostPwdDesignCore::link('user') }}
        {if Framework\Mvc\Model\DbConfig::getSetting('userActivationType') == Registration::EMAIL_ACTIVATION}
            | <a rel="nofollow" href="{{ $design->url('user','main','resendactivation') }}">{lang 'Resend activation email'}</a>
        {/if}
    </p>
</div>

<div class="col-md-4 ad_336_280">
    {designModel.ad(336, 280)}
</div>
