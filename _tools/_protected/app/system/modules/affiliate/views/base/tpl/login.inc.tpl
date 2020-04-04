<div class="aff_login">
  <h2>{lang 'Sign In'}</h2>
  {{ LoginForm::display(300) }}

  <p class="small">
    {{ LostPwdDesignCore::link('affiliate') }}
    {if Framework\Mvc\Model\DbConfig::getSetting('affActivationType') == Registration::EMAIL_ACTIVATION}
      | <a rel="nofollow" href="{{ $design->url('affiliate','home','resendactivation') }}">{lang 'Resend activation email'}</a>
    {/if}
  </p>
</div>
