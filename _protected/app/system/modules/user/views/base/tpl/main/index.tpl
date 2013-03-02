{@if(!User::auth())@}

  {@if($is_splash_page)@}

    {@manual_include('index.guest_splash.inc.tpl')@}

  {@else@}

    {@manual_include('index.guest.inc.tpl')@}

  {@/if@}

{@else@}

  {@manual_include('index.user.inc.tpl')@}

{@/if@}
