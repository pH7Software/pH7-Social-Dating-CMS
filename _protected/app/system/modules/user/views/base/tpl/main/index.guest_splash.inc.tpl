{* Enable the Splash Background Video if it is enabled and if the visitor is not from a mobile device *}
{if $is_bg_video && !$browser->isMobile()}
    <video autoplay loop poster="{url_tpl_img}splash_vid.jpg" id="bgvid">
        <source src="{url_tpl}file/splash.webm" type="video/webm" />
    </video>
{/if}

<div class="login_block animated fadeInDown">
  {{ LoginSplashForm::display(290) }}
</div>

<div class="right animated fadeInRight">
  <h1 class="pink2 italic underline">{lang 'Be on the best place to meet people!'}</h1>
  {{ JoinForm::step1() }}
</div>

<div class="left animated fadeInLeft">
  <h1 class="pink2 italic underline s_bMarg">{slogan}</h1>
  <div class="center profiles_window thumb">
    {{ $userDesignModel->profiles() }}
  </div>



  <div class="center s_tMarg">
    <h2>{lang 'Meet people in %0% with %site_name%!', $design->geoIp(false)}</h2>
    {promo_text}
  </div>
</div>
