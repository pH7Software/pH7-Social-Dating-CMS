<div class="left">

  <p>{lang 'Not registered yet?'}<br />
  {lang 'Come quickly'} <a href="{{ $design->url('user','signup','step1') }}"><strong>{lang 'register!'}</strong></a></p>

  {{ LoginForm::display() }}

  <p>
    {{ LostPwdDesignCore::link('user') }}
    {if Framework\Mvc\Model\DbConfig::getSetting('userActivationType') == 2} | <a rel="nofollow" href="{{ $design->url('user','main','resendactivation') }}">{lang 'Resend activation email'}</a>{/if}
  </p>

</div>

<div class="right">

  {* Show promotional images (you can change the images from the "/static/img/promo/" folder *}
  <p class="pic thumb"><img src="{url_static_img}promo/login{% mt_rand(1,2) %}_400x280.jpg" alt="{lang 'Free Online Dating Site'}" title="{lang 'Free Online Dating Site'}"></p>

</div>
