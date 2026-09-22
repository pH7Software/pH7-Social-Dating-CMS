<div class="aff_login">
  <h2>{lang 'Sign In'}</h2>
  {* Showing the login form signs out a member or admin, so they get a link to its own page instead *}
  {if UserCore::auth() OR AdminCore::auth()}
    <p><a rel="nofollow" href="{{ $design->url('affiliate','home','login') }}">{lang 'Login Affiliate'}</a></p>
  {else}
    {{ LoginForm::display(320) }}

    <p class="small">
      {{ LostPwdDesignCore::link('affiliate') }}
      {if Framework\Mvc\Model\DbConfig::getSetting('affActivationType') == Registration::EMAIL_ACTIVATION}
        | <a rel="nofollow" href="{{ $design->url('affiliate','home','resendactivation') }}">{lang 'Resend activation email'}</a>
      {/if}
    </p>
  {/if}
</div>
