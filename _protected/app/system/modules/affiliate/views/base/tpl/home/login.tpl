<p>{lang 'Not yet registered on our affiliate program?'}<br />
{lang 'Come quickly'} <a href="{{ $design->url('affiliate','signup','step1') }}">{lang 'register on affiliate platform!'}</a></p>

{{ LoginForm::display() }}

<p>
  {{ LostPwdDesignCore::link('affiliate') }}
  {if Framework\Mvc\Model\DbConfig::getSetting('affActivationType') == 2} | <a rel="nofollow" href="{{ $design->url('affiliate','home','resendactivation') }}">{lang 'Resend activation email'}</a>{/if}
</p>
