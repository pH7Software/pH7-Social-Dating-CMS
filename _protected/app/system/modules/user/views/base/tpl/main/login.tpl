<div class="left">

  <p>{lang 'Not registered yet?'}<br />
  {lang 'Come quickly'} <a href="{{ $design->url('user','signup','step1') }}"><strong>{lang 'register!'}</strong></a></p>

  {{ LoginForm::display() }}

  <p>{{ LostPwdDesignCore::link('user') }}
    {if Framework\Mvc\Model\DbConfig::getSetting('userActivationType') == 2} | <a rel="nofollow" href="{{$design->url('user','main','resendactivation')}}">{lang 'Resend activation email'}</a> {/if}
  </p>

</div>

<div class="right">

  {* Show promotional image chosen from the list *}
  {{ $img_list = ['girls', 'match'] }}
  {{ $img_name = $img_list[mt_rand(0,1)] }}

  <p><a class="pic thumb" href="http://cool-on-web.com"><img src="http://static.cool-on-web.com/img/promo/{img_name}-social-dating-400x280.jpg" alt="Free Online Dating" title="Free Online Dating with cool on Web!"></a></p>

</div>
