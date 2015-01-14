<div class="login_block">
  {{ LoginSplashForm::display(290) }}
</div>

<div class="right">
  <h1 class="pink2 italic underline">{lang 'Welcome to %site_name%!'}</h1>
  {{ JoinForm::step1() }}
</div>

<div class="left">
  <h1 class="pink2 italic underline s_bMarg">{slogan}</h1>
  <div class="center profiles_window thumb">
    {{ $userDesignModel->profiles() }}
  </div>

  <div class="center s_tMarg">
    <h2>{lang 'Meet people in %0% with %site_name%!', $design->geoIp(false)}</h2>
    {promo_text}
  </div>
</div>
