<div class="login_block">
    {{ LoginSplashForm::display(290) }}
    <div class="bt_login_forgot">{{ LostPwdDesignCore::link('user') }}</div>
</div>

<div class="right">
    <h1 class="pink2 italic underline">{@lang('Welcome to %site_name%!')@}</h1>
    {{ JoinForm::step1() }}
</div>

<div class="left">
    <h1 class="pink2 italic underline">{@lang('Free Online Dating Social Community Site with Chat Rooms')@}</h1><br />
    <div class="center profiles_window thumb">
      {{ $userDesignModel->profiles() }}<br />
    </div>

    <div class="center">
      <br /><br />
      <h2>{@lang('Meet new people in')@} {{ $design->geoIp() }} {@lang('with %site_name%')@}</h2>
      <p>{@lang('This is the best place for meeting new people nearby. Chat, flirt, socialize and have fun!')@}<br />
      {slogan}</p>
    </div>
</div>
